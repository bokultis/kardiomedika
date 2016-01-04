<?php

class Application_Model_CountryMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Application_Model_CountryMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Application_Model_DbTable_Country
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_Country();
    }

    /**
     * get instance
     *
     *
     * @return Application_Model_CountryMapper
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
     * Get country by id from db
     *
     * @param int $id
     * @param Application_Model_Country $country
     * @return boolean
     */
    public function getCountryById($id,$country) {

        $result = $this->_dbTable->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $country->setOptions($row->toArray());
        return true;
    }

    /**
     * Get country by code2 from db
     *
     * @param string $code
     * @param Application_Model_Country $country
     * @return boolean
     */
    public function getCountryByCode($code,$country) {

        $select = $this->_dbTable->select()->from('country', '*')->where('code2 = ?', strtoupper($code));
        $result = $this->_dbTable->fetchAll($select);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $country->setOptions($row->toArray());
        return true;
    }

    /**
     * Get country by ip address from geoip db
     *
     * @param string $ip
     * @param Application_Model_Country $country
     * @return boolean
     */
    public function getCountryByGeoIp($ip,$country) {
	$ipLong = sprintf("%u",ip2long($ip));
//        $select = $this->_dbTable->select();
//        $select ->from(array('c' => 'country'), array('c.*'))
//                ->joinLeft(array('cg' => 'country_geoip'), 'cg.country_code2 = c.code2', array())
//                ->joinLeft(array('ct' => 'country_translate'), 'ct.code2 = c.code2', array())
//                ->where('cg.min_long <= ?', $ipLong)
//                ->where('cg.max_long >= ?', $ipLong)
//                ->where('ct.language = ?', CURR_LANG)
//                ;
//        Zend_Debug::dump($select->__toString());die;
//        $result = $this->_dbTable->fetchAll($select);
        
        $query = "  SELECT c.*,ct.value AS country_tr
                    FROM country AS c
                        LEFT JOIN country_translate AS ct ON c.code2 = ct.code2 AND ct.language = '" . CURR_LANG . "'
                        LEFT JOIN country_geoip AS cg ON cg.country_code2 = c.code2 
                    WHERE  cg.min_long <= '" . $ipLong . "' AND cg.max_long >= '" . $ipLong . "' ORDER BY ct.value ASC";
        $result = $this->_dbTable->getAdapter()->fetchAll($query);
        
        if (0 == count($result)) {
            return false;
        }
        
        
        if($result[0]['country_tr']){
                $result[0]['name'] = $result[0]['country_tr'];
            }
        
        $country->setOptions($result[0]);
        return true;
    }

    /**
     * Get all countries where dial code is not null
     * @param string $language language code
     * @return array
     */
    public function getAllCountries($language = null) {
        if(!isset ($language)) {
            $language = 'en';
        }
        $query = "  SELECT c.*,ct.value AS country_tr
                    FROM country AS c
                        LEFT JOIN country_translate AS ct ON c.code2 = ct.code2 AND ct.language = '" . addslashes($language) . "'
                    WHERE c.dial_code is not NULL ORDER BY ct.value ASC";
        $resultSet = $this->_dbTable->getAdapter()->fetchAll($query);
        $countries   = array();
        foreach ($resultSet as $row) {
            $country = new Application_Model_Country();
            if($row['country_tr']){
                $row['name'] = $row['country_tr'];
            }
            $country->setOptions($row);
            $countries[] = $country;
        }
        return $countries;
    }
}

?>