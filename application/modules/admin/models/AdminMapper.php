<?php

/**
 * Admin Mapper
 *
 * @package Application
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Admin_Model_AdminMapper extends HCMS_Model_Mapper {

    /**
     * singleton instance
     *
     * @var Admin_Model_AdminMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function __construct() {
        $this->_dbTable = new Admin_Model_DbTable_Admin();
    }

    /**
     * get instance
     * @return Admin_Model_AdminMapper
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Find admin
     *
     * @param int $applicationId
     * @param string $username
     * @param Admin_Model_Admin $admin
     * @return boolean
     */
    public function find($applicationId, $username,Admin_Model_Admin $admin) {

        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('a' => 'admin'), array('a.*'))
                ->joinLeft(array("aa"=>"admin_application"), "aa.admin_id = a.id")
                ->where('a.username = ?', $username)
                ->where('aa.application_id = ?', $applicationId);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }

        //get first row from resultSet
        $row = $resultSet->current();
        $admin->setOptions($row->toArray());
        return true;
    }
}