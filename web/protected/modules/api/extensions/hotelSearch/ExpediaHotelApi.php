<?php
/**
 * ExpediaHotelApi class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2014, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * Model for expedia api requests
 * @package application.extensions.hotelSearch
 */
class ExpediaHotelApi extends PTApiRequest
{
    /**
     * builds url for hotel list api request
     * @param array $options requets params
     * @return string hotelList api url
     */
    public function getHotelListQuery($options = array())
    {
        $options = array_merge(array(
            // 'destinationId'=>'84F1DA8F-F50C-48CE-9008-D70EDB8228DF'   // 1  - 4 ways to find hotels
            // 'destinationString' => 'Izhevsk, Russia'                  // 2
            // 'hotelIdList'=>"1,2,3"                                    // 3
            // "city"                  => "Moscow",                      // 4
            // "stateProvinceCode"     => "WA",                          // 4 only for US, CA, AU 
            // "countryCode"           => "RU",                          // 4
            //'minorRev'  => "current minorRev #",
            "cid"                   => 55505,
            "apiKey"                => Yii::app()->params['ean']['Key'], // your secret key
            "numberOfResults"       => 5,
            "sort"                  => "PRICE",
            //"customerUserAgent"     => "xxx",
            //"customerIpAddress"     => "xxx",
            "locale"                => "en_US",
            "currencyCode"          => "RUB",
            "arrivalDate"           => "04/20/2014",
            "departureDate"         => "04/25/2014",
            "room1"                 => "1",     // "2,4,5" - 2 adults and children of 4 and 5 years old
            "supplierCacheTolerance"=> "MED_ENHANCED",
            //'cacheKey'            => "-d9adc4:14530da5dc3:-7b53", // for pagination from previous request
            //'cacheLocation'       => "10.186.170.75:7300",        // for pagination from previous request
            // filtering (maxStarRating, minStarRating, minRate, maxRate etc)
            //'propertyCategory'  => 1 // 1: hotel 2: suite 3: resort 4: vacation rental/condo 5: bed & breakfast 6: all-inclusive 
        ), $options);
        $result = $this->buildUrl('http://api.ean.com/ean-services/rs/hotel/v3/list', $options);
        return $result;
    }
}
