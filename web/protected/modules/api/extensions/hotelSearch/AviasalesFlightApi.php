<?php
/**
 * AviasalesFlightApi class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2014, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * Model for aviasales api requests
 * @package application.exensions.hotelSearch
 */
class AviasalesFlightApi extends PTApiRequest
{
    /**
     * @var array reponse result
     */
    public $response = array();
    /**
     * @var string aviasales api base url
     */
    public $priceMapUrl = "http://map.aviasales.ru/prices.json";
    /**
     * gets api priceMap data (lowest prices API)
     * @param array $options additional options for api request
     * @return \AviasalesFlightApi self instance
     */
    public function getPriceMap($options = array())
    {
        $this->request($this->priceMapUrl, array_merge(array(
            'origin_iata'  => 'MOW',
            'period'    => "",//'2014-03-01:season',
            'direct'    => "false",
            'one_way'   => "false",
            "price"     => 50000,
            "no_visa"   => "true",
            "schengen"  => "true",
            "need_visa" => "true",
            "locale"    => "ru",
            "currency"  => "rub",
            "min_trip_duration_in_days"=>13,
            "max_trip_duration_in_days"=>15
        ), $options));
        return $this;
    }
    /**
     * sorts response by field
     * @param string $field response array key
     * @return \AviasalesFlightApi self instance
     */
    public function sort($field)
    {
        usort($this->response, function($a, $b) use($field){
            return $a[$field] > $b[$field];
        });
        return $this;
    }
    /**
     * crops reponse from offset with array length 
     * @param integer $offset response data start from
     * @param integer $length response data length
     * @return \AviasalesFlightApi self instance
     */
    public function crop($offset, $length)
    {
        $this->response = array_slice($this->response, $offset, $length);
        return $this;
    }
    /**
     * filters response data according to $filters rules
     * @param array $filters filters like array('city'=>array('in', array('Moscow', 'London')))
     * @return \AviasalesFlightApi self instance
     */
    public function filter($filters = array())
    {
        foreach($filters as $field=>$condition){
            foreach($this->response as $key=>$data){
                switch ($condition[0]){
                    case "in" : if(!in_array($data[$field], $condition[1])){
                            unset($this->response[$key]);
                        }
                        break;
                    case '>'  : if($data[$field] < $condition[1]){
                            unset($this->response[$key]);
                        }
                        break;
                    case '<'  : if($data[$field] > $condition[1]){
                            unset($this->response[$key]);
                        }
                        break;
                    case '='  : if($data[$field] == $condition[1]){
                            continue;
                        }
                    break;
                }
            }
        }
        return $this;
    }
    /**
     * groups response by key field
     * @param string $field reponse key name
     * @return \AviasalesFlightApi self instance
     */
    public function group($field)
    {
        $result = array();
        foreach($this->response as $data){
            if(isset($result[$data[$field]])){
                continue;
            }
            $result[$data[$field]] = $data;
        }
        $this->response = $result;
        return $this;
    }
    /**
     * requests api
     * @param string $url api url
     * @param array $options api request options
     * @return \AviasalesFlightApi self instane
     */
    private function request($url, $options)
    {
        if(!is_array($options['origin_iata'])){
            $options['origin_iata'] = array($options['origin_iata']);
        }
        $airports = $this->getAirports();
        foreach ($options['origin_iata'] as $origin_iata){
            $options['origin_iata'] = $origin_iata;
            $request = $this->buildUrl($url, $options);
            $response = json_decode(file_get_contents($request), true);
            if(!is_array($response)){
                continue;
            }
            foreach($response as $data){
                $airportData = @$airports[$data['destination']];
                $data['departureAirportCode'] = $origin_iata;
                $data['city'] = @$airportData['city'];
                $data['country'] = @$airportData['country'];
                $data['countryCode']  = @$airportData['countryCode'];
                $this->response[] = $data;
            }
        }
        return $this;
    }
    /**
     * gets all airports data
     * @return array airports data
     */
    public function getAirports()
    {
        return include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '_airports.php';
    }
}
