<?php
/**
 * Application Mapper
 *
 * @package Application
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Application_Model_ApplicationMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Application_Model_ApplicationMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Application_Model_ApplicationDbTable
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_Application();
    }

    /**
     * get instance
     *
     *
     * @return Application_Model_ApplicationMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get cache id for app
     * 
     * @param int $applicationId
     * @return string
     */
    private function _getCacheId($applicationId){
        return 'app' . $applicationId;
    }

    /**
     * Load app from cache
     *
     * @return boolean
     */
    private function _loadAppFromCache($id, Application_Model_Application &$application){
        $cacheId = $this->_getCacheId($id);
        $cache = HCMS_Cache::getInstance()->getCoreCache();
        if( ($result = $cache->load($cacheId)) === FALSE ){
            return FALSE;
        }
        $application = $result;

        //$this->_initLogger();
        //$this->_log("App loaded from cache", Zend_Log::INFO);
        return true;
    }

    /**
     * Save app to cache
     * 
     * @param Application_Model_Application $application
     * @return boolean
     */
    private function _saveAppToCache(Application_Model_Application $application){
        HCMS_Cache::getInstance()->getCoreCache()->save($application, $this->_getCacheId($application->get_id()));
        //$this->_initLogger();
        //$this->_log("App saved to cache", Zend_Log::INFO);
        return true;
    }

    /**
     * Invalidate app cache
     *
     * @param int $applicationId
     */
    public function cleanCache($applicationId){
        $this->_initLogger();
        $this->_log("App cache cleaned", Zend_Log::INFO);
        HCMS_Cache::getInstance()->getCoreCache()->remove($this->_getCacheId($applicationId));
    }

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Application_Model_Application $application
     * @return boolean
     */
    public function find($id, Application_Model_Application &$application) {
        if($this->_loadAppFromCache($id, $application)){
            return true;
        }
        $result = $this->_dbTable->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        self::rowToEntity($row, $application);
        $this->_saveAppToCache($application);
        return true;
    }

    /**
     * Find all Applications
     * @param array $criteria
     *
     * @return array
     */
    public function fetchAll($criteria = array()) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
            ->from(array('a'=>'application'),array('a.*'));
        if(isset ($criteria['status'])){
            $select->where('a.status = ?', $criteria['status']);
        }
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $applications   = array();
        foreach ($resultSet as $row) {
            $application = new Application_Model_Application();
            self::rowToEntity($row, $application);
            $applications[] = $application;
        }
        return $applications;
    }

    /**
     * This function is used to decoding signed_request data
     * more information is here http://developers.facebook.com/docs/authentication/signed_request
     */
    public static function parseSignedRequest($signed_request, $secret) {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        // decode the data
        $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            error_log('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }

        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    /**
     * Find and populate entity by signed request
     *
     * @param string $signedRequest
     * @param Application_Model_Application $application
     * @return boolean
     */
    public function findBySignedRequest($signedRequest, Application_Model_Application &$application) {
        $apps = $this->fetchAll();
        /* @var $currApplication Application_Model_Application */
        foreach ($apps as $currApplication) {
            $decodedSignedRequest = self::parseSignedRequest($signedRequest, $currApplication->get_fb_settings('api_secret'));
            if($decodedSignedRequest != null){
                $application = $currApplication;
                $application->set_signed_request($decodedSignedRequest);
                return true;
            }            
        }
        return false;
    }

    /**
     * Covert entity to array
     * 
     * @param Application_Model_Application $application
     * @param boolean $notNull
     */
    public static function getData(Application_Model_Application $application, $notNull = true){
        $data = array();
        $fields = array('id','name','status','status_dt','style_json','fb_settings','twitter_settings','email_settings','settings','og_setings');

        foreach ($fields as $field) {
            $value = $application->__get($field);
            if($value != null || !$notNull){
                if(in_array($field, array('style_json','fb_settings','twitter_settings','email_settings','settings','og_setings'))){
                    $value = json_encode($value);
                }
                $data[$field] = $value;
            }
        }

        return $data;
    }
    
    /**
     * Save entity
     *
     * @param Application_Model_Application Application
     */
    public function save(Application_Model_Application $application) {
        $data = array();

        $this->_entityToRow($application, $data);
//        print_r($data);die;
        $id = $application->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $application->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
        $this->cleanCache($application->get_id());
    }

    /**
     * Convert DB row to entity object
     * 
     * @param Zend_Db_Table_Row_Abstract $row
     * @param Application_Model_Application $application
     */
    public static function rowToEntity(Zend_Db_Table_Row_Abstract $row, Application_Model_Application $application){
        $application    ->set_email_settings(self::getJsonSetings($row->email_settings))
                        ->set_fb_settings(self::getJsonSetings($row->fb_settings))
                        ->set_twitter_settings(self::getJsonSetings($row->twitter_settings))
                        ->set_og_settings(self::getJsonSetings($row->og_settings))
                        ->set_id($row->id)
                        ->set_name($row->name)
                        ->set_status($row->status)
                        ->set_status_dt($row->status_dt)
                        ->set_style_json(self::getJsonSetings($row->style_json))
                        ->set_settings(self::getJsonSetings($row->settings));
    }
    
    protected function _entityToRow(Application_Model_Application $application,array &$row){
        $this->_populateDataArr($row, $application, array('id','name','status','status_dt',
                'style_json','fb_settings','twitter_settings','email_settings','settings','og_settings'),array('style_json','fb_settings','twitter_settings','email_settings','settings','og_settings'));
    }
    
    private static function getJsonSetings($json){
        return (isset ($json) && $json != '')?json_decode($json, true):array();
    }
}