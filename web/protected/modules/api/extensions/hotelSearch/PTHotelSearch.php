<?php
/**
 * PTHotelSearch class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2014, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * Model for preparing all api requests
 * @package application.extensions.hotelSearch
 */
class PTHotelSearch
{
    /**
     * @var array original api request query
     */
    public $query       = array();
    /**
     * @var array api query for flights
     */
    public $flightQuery = array();
    /**
     * @var array api query for hotels
     */
    public $hotelQuery  = array();
    /**
     * @var api reponse flights 
     */
    public $flights     = array();
    /**
     * @var array api response hotels
     */
    public $hotels      = array();
    /**
     * @var array final result
     */
    public $result      = array();
    /**
     * @var array all hotels url foor api requests
     */
    public $hotelUrls   = array();
    /**
     * @var array all flight api result destination ids
     */
    public $destinationIds = array();
    
    
    /* INIT */
    
    /**
     * creates self instance
     * @param array $query api request original query
     * @return \self self instance
     */
    public static function model($query = array())
    {
        $result =  new self;
        $result->query = array_merge($result->query, $query);
        return $result;
    }
    /**
     * processes request with fluid interface
     * @return \PTHotelSearch self instance
     */
    public function process()
    {
        $this->prepareFlightQuery()
            ->prepareHotelQuery()
            ->setFlights()
            ->setHotelUrls()
            ->setHotels()
            ->compactHotels();
        return $this;
    }
    
    
    /* APIs */
    /**
     * returns hotel api component
     * @return \ExpediaHotelApi hote api
     */
    public function hotelApi()
    {
        return new ExpediaHotelApi();
    }
    /**
     * returns flight api component
     * @return \AviasalesFlightApi flight api
     */
    public function flightApi()
    {
        return new AviasalesFlightApi();
    }
    
    
    /* FLUID METHODS */
    /**
     * makes flight api request params from original request
     * @return \PTHotelSearch this
     */
    private function prepareFlightQuery()
    {
        if(!$this->query){
            return $this;
        }
        if($from = @$this->query['from']){
            $this->flightQuery['origin_iata'] = $from;
        }        
        return $this;
    }
    /**
     * makes hotel api request params from original request
     * @return \PTHotelSearch this
     */
    private function prepareHotelQuery()
    {
        if(!$this->query){
            return $this;
        }
        if($people = @$this->query['people']){
            $this->hotelQuery["room1"] = @$people['adults'] ? $people['adults'] : 1;
            if($children = @$people['children']){
                $this->hotelQuery['room1'] .= "," . implode(",", $children);
            }
        }
        return $this;
    }
    /**
     * gets and processes flight api response
     * @return \PTHotelSearch this
     */
    private function setFlights()
    {
        $filter = array('destination' => array('in', array_keys($this->allDestinations())));
        if($from = @$this->query['start']['from']){
            $filter['depart_date'] = array('>', $this->convertDate($from, 'Y-m-d'));
        }
        if($to = @$this->query['finish']['to']){
            $filter['return_date'] = array('<', $this->convertDate($to, 'Y-m-d'));
        }
        $this->flights = $this->flightApi()->getPriceMap($this->flightQuery)
            ->filter($filter)
            ->sort('value')
            ->crop(0, 10)
            ->group('destination')
            ->response;
        return $this;
    }
    /**
     * creates hotel api request urls
     * @return \PTHotelSearch this
     */
    private function setHotelUrls()
    {
        foreach($this->flights as $flight){
            $this->hotelUrls[] = $this->hotelApi()->getHotelListQuery(
                array_merge($this->hotelQuery, array(
                    'arrivalDate'   => $this->convertDate($flight['depart_date'], 'm/d/Y'),
                    'departureDate' => $this->convertDate($flight['return_date'], 'm/d/Y'),
                    'city'          => $flight['city'],
                    'countryCode'   => $flight['countryCode']
                ))
            );
        }
        return $this;
    }
    /**
     * gets and processe hotels api response
     * @return \PTHotelSearch this
     */
    private function setHotels()
    {
        $response = $this->hotelApi()->rollingCurl($this->hotelUrls)->rollingResponse;
        foreach($response as $json){
            $data = json_decode($json, true);
            if(!$hotels = @$data['HotelListResponse']['HotelList']['HotelSummary']){
                continue;
            }
            $this->hotels = array_merge(
                $this->hotels, (isset($hotels['hotelId']) ? array($hotels) : $hotels)
            );
        }
        return $this;    
    }
    /**
     * creates final result compacting hotels+flights
     * @return \PTHotelSearch this
     */
    private function compactHotels()
    {
        foreach($this->hotels as $hotel){
            $airportCode = @$hotel['airportCode'];
            if(!$flight = @$this->flights[$airportCode]){
                continue;
            }
            $hotelCost = $hotel['highRate'] * $this->getDaysRange($flight['depart_date'], $flight['return_date']);
            $flightCost = $flight['value'] * $this->query['people']['adults'];
            $this->result[$hotel['name'].$flight['value']] = array(
                'departureAirportCode'  => $flight['departureAirportCode'],   
                'name'      => $hotel['name'],
                'city'      => $hotel['city'],
                'countryCode'=>$hotel['countryCode'],
                'country'   => $this->getCodeCountry($hotel['countryCode']),
                "start"     => $this->convertDate($flight['depart_date'], 'Ymd'),
                "finish"    => $this->convertDate($flight['return_date'], 'Ymd'),
                "flightCost"=> $flightCost,
                'hotelCost' => $hotelCost,
                "cost"      => $flightCost + $hotelCost,
            );
        }
        return $this;
    }
    /**
     * sorts final result by the field
     * @param string $field field name to sort by
     * @param string $dir sort direction // TODO
     * @return \PTHotelSearch this
     */
    public function sort($field, $dir = 'ASC')
    {
        usort($this->result, function($a, $b) use($field){
            return $a[$field] > $b[$field];
        });
        return $this;
    }
    
    
    /* HELPERS */
    /**
     * gets days range from start till end date
     * @param string $start start date
     * @param string $end end date
     * @return integer count of days
     */
    public function getDaysRange($start, $end)
    {
        return round((strtotime($end) - strtotime($start)) / (24*60*60));
    }
    /**
     * converts given date to another format
     * @param string $date date
     * @param string $format date format like 'Y-m-d' etc
     * @return string date
     */
    public function convertDate($date, $format)
    {
        return date($format, strtotime($date));
    }
    /**
     * gets all airports data
     * @return array destinations
     */
    public function allDestinations()
    {
        return include dirname(__FILE__).DIRECTORY_SEPARATOR.'_destinations.php';
    }
    /**
     * returns countries by code keys
     * @param integer $num iso version number
     * @return array countries
     */
    public function getCodeCountries($num = 3)
    {
        switch($num){
            case 3: return include dirname(__FILE__).DIRECTORY_SEPARATOR.'_codeCountries.php';
            case 2: return include dirname(__FILE__).DIRECTORY_SEPARATOR.'_codeCountries2.php';
        }
    }
    /**
     * gets country name by code
     * @param string $code country code
     * @return string country name
     */
    public function getCodeCountry($code)
    {
        $countries = $this->getCodeCountries(strlen($code));
        return @$countries[$code];
    }
}
