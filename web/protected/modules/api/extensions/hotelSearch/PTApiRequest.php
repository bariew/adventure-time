<?php
/**
 * Model class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2014, Bariev Pavel
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
/**
 * Bas model for api requests
 * @package application.extensions.hotelSearch
 */
class PTApiRequest
{
    /**
     * @var array api request options
     */
    public $options = array();
    /**
     * @var array response from rolling curl
     */
    public $rollingResponse = array();
    /**
     * inits model, sets api options
     * @param array $options api options
     */
    public function __construct($options = array())
    {
        $this->options = array_merge($this->options, $options);
    }
    /**
     * requests all urls by curl simultaneously
     * @param array $allUrls urls for requests
     * @param integer $size rolling curl simultaneous requests count 
     * @return \PTApiRequest self instance
     */
    public function rollingCurl($allUrls, $size = 5)
    {
        $rc = new RollingCurl(array($this, 'rollingCurlCallback'));
        $rc->window_size = $size;
        $urlChunks = array_chunk($allUrls, $size);
        foreach($urlChunks as $key=>$urls){
            foreach ($urls as $url) {
                $request = new RollingCurlRequest($url);
                $rc->add($request);
            }
            $rc->execute();
            if(isset($urlChunks[$key+1])){
                sleep(1);
            }
        }

        return $this;
    }
    /**
     * method for each response processing
     * @param string $response single response data
     * @param array $info curl info
     * @param string $request single request url
     */
    public function rollingCurlCallback($response, $info, $request) 
    {
        $this->rollingResponse[] = $response;
    }
    /**
     * builds url from url and params
     * @param string $url request url
     * @param array $data request params
     * @return string url
     * @author hackerone
     */
    public function buildUrl($url, $data = array())
    {
        $parsed = parse_url($url);
        isset($parsed['query']) ? parse_str($parsed['query'], $parsed['query']) : $parsed['query'] = array();
        $params = isset($parsed['query']) ? array_merge($parsed['query'], $data) : $data;
        $parsed['query'] = ($params) ? '?' . http_build_query($params) : '';
        if (!isset($parsed['path'])) {
            $parsed['path']='/';
        }

        $parsed['port'] = isset($parsed['port'])?':'.$parsed['port']:'';

        return $parsed['scheme'].'://'.$parsed['host'].$parsed['port'].$parsed['path'].$parsed['query'];
    }    
}
