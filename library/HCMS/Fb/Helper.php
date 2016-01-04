<?php
/**
 * Fb Helper Class
 *
 * @package HCMS
 * @subpackage Fb
 * @copyright Horisen
 * @author zeka
 */
class HCMS_Fb_Helper {
    /**
     * Get facebook resources
     * @param string $url
     * @return array 
     */
    public static function getFbResources($url) {
        $config = array(
            'adapter'      => 'Zend_Http_Client_Adapter_Socket',
            'ssltransport' => 'tls'
        );
        $client = new Zend_Http_Client($url, $config);
        $response = $client->request();
        return  json_decode($response->getBody(), true);
    }

}