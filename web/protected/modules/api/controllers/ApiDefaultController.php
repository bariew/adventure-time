<?php
/**
 * ApiController class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2014, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
Yii::import("api.extensions.hotelSearch.*");
/**
 * Controllers for API requests
 * @package application.modules.api.controllers
 */
class ApiDefaultController extends FrontendController
{
    /*
     * main api response action
     * gets hotels+flights according to params
     * @example $.post('/api/search', '{"from"  : ["MOW"], "start" : {"from"  : "20140406", "to"    : "20140408"}, "finish"    : {"from"  : "20140513","to"    : "20140515"}, "visa"      : "any",  "people"    : {"adults"    : 2, "children"  : []}}')    
     */
    public function actionSearch()
    {
        $query = (($request = $this->extractRequest()) && is_array($request))
            ? $request
            : array(
                "from"  => array('MOW'),
                "start" => array(
                    "from"  => $this->makeDate("+1day"),
                    "to"    => $this->makeDate("+1week")
                ),
                "finish"=> array(
                    "from"  => $this->makeDate("+3week"),
                    "to"    => $this->makeDate("+6week")
                ),
                "visa"      => "no",
                "schengen"  => "no",
                "people"    => array(
                    "adults"    => 1,
                    "children"  => array()
                )
            );
        $hash = md5(json_encode($request));
        $response = ($cache = Yii::app()->cache->get($hash))
            ? unserialize($cache)
            : PTHotelSearch::model($query)->process()->sort('cost')->result;
        Yii::app()->cache->set($hash, serialize($response), (defined('PRODUCTION') ? 300 : 1));
        Yii::log(json_encode(compact('request', 'response'), true), 'info', 'api');
        $this->renderJson($response);
    }
    /**
     * renders json data
     * @param array $data data to display
     */
    public function renderJson($data)
    {
        header('Content-type: application/json');
        echo CJSON::encode($data);
    }
    /**
     * gets json params from post body
     * @return array request body
     */
    private function extractRequest()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
    /**
     * converts given date to another format
     * @param string $date original date 
     * @param string $format date format
     * @return string date
     */
    private function makeDate($date, $format='Ymd')
    {
        $date = new DateTime($date);
        return $date->format($format);
    }
    
    
    /* DEVELOPMENT */
    
    public function actionTestFlight()
    {
        $curl = Yii::app()->curl;
        $curl->setHeaders(array(
            'X-Access-Token'    => Yii::app()->params['travelpayouts']['token']
        ));
        //'http://api.aviasales.ru/v1/cities/BER/directions/BCN/prices.json'
        $data = $curl->get('http://api.aviasales.ru/v1/cities/BER/directions/-/prices.json', array(
            'departure_at'  => '2014-04',
            'return_at'     => '2014-04',
            'currency'      => 'RUB',
            'length'        => 5
            //'token'         => '31241'
        ));
        print_r($data);
    }
}
