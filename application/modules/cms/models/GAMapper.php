<?php

/**
 * Google Analytics API Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_GAMapper {

    /**
     * singleton instance
     *
     * @var Cms_Model_GAMapper
     */
    protected static $_instance = null;

    /**
     * private constructor
     */
    private function __construct() {

    }

    /**
     * get instance
     *
     *
     * @return Cms_Model_GAMapper
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get Cache id
     * @param array $params
     * @return string
     */
    private function _getCacheId(array $params) {
        return 'ga_' . md5(implode('-', $params));
    }

    /**
     * get cache object
     * 
     * @return Zend_Cache_Core
     */
    private function _getCacheObject() {
        return HCMS_Cache::getInstance()->getCustomCache('core', array(
            'frontend' => array(
                'name' => 'Core',
                'options' => array(
                    'lifetime' => 86400,
                    'automatic_serialization' => true
                )
            ))
        );
    }

    /**
     * Parse returned str to timestamp
     * 
     * @param string $dateStr
     * @return int
     */
    private function _parseDateString($dateStr){
        $year = substr($dateStr, 0, 4);
        $month = substr($dateStr, 4, 2);
        $day = substr($dateStr, 6, 2);
        return mktime(0, 0, 0, $month, $day, $year);
    }

    /**
     * Get data by date dimension
     * 
     * @param string $email
     * @param string $password
     * @param string $accountId
     * @param string $startDate
     * @param string $endDate
     * @param array $metrics
     */
    public function fetchByDate($email, $password, $accountId, $startDate, $endDate, array $metrics = array()) {
        //default metrics
        if (count($metrics) == 0) {
            $metrics = array('ga:visits', 'ga:visitors', 'ga:pageviews', 'ga:avgTimeOnSite', 'ga:visitBounceRate', 'ga:percentNewVisits');
        }
        $cacheId = $this->_getCacheId(array_merge(array($email, $accountId, $startDate, $endDate), $metrics));
        $cache = $this->_getCacheObject();
        //try load from cache
        if( ($statistics = $cache->load($cacheId)) === false ) {
            $statistics = array();
            //loading via api
            $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, HCMS_Gdata_Analytics::AUTH_SERVICE_NAME);
            $service = new HCMS_Gdata_Analytics($client);
            //query
            $query = $service->newDataQuery();
            $query->setProfileId($accountId)
                    ->addDimension('ga:date')
                    ->setStartDate($startDate)
                    ->setEndDate($endDate);
            foreach ($metrics as $metric) {
                $query->addMetric($metric);
            }
            
            $result = $service->getDataFeed($query);
            foreach ($result as $row) {
                $currDate = $this->_parseDateString($row->getValue('ga:date')->getValue());
                //Zend_Debug::dump($currDate);
                $statistics[$currDate] = array();
                foreach ($metrics as $metric) {
                    $statistics[$currDate][$metric] = $row->getMetric($metric)->getValue();
                }
            }
            //save to cache
            $cache->save($statistics, $cacheId);
        }
        return $statistics;
    }

}