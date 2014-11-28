<?php
/**
 * Module Mapper
 *
 * @package Application
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Application_Model_ModuleMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Application_Model_ModuleMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Application_Model_DbTable_Module
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_Module();
    }

    /**
     * get instance
     *
     *
     * @return Application_Model_ModuleMapper
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
     * Convert row to module entity
     * 
     * @param Zend_Db_Table_Row_Abstract $row
     * @param Application_Model_Module $module
     */
    private function _rowToModule(Zend_Db_Table_Row_Abstract $row,Application_Model_Module $module){
        $module->setOptions($row->toArray());
        $module ->set_settings($this->_getJsonData($row->settings))
                ->set_data($this->_getJsonData($row->data));        
    }

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Application_Model_Module $module
     * @return boolean
     */
    public function find($id, Application_Model_Module $module) {
        $result = $this->_dbTable->find($id);
        if (0 == count($result)) {
            return false;
        }
        $this->_rowToModule($result->current(), $module);
        return true;
    }

    /**
     * Find by module dir
     * 
     * @param string $code
     * @param Application_Model_Module $module
     * @return boolean
     */
    public function findByCode($code, Application_Model_Module $module) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('m'=>'module'),array('m.*'))
                ->where('m.code = ?', $code);
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $this->_rowToModule($resultSet->current(), $module);
        return true;
    }

    /**
     * Find all modules
     * @param array $criteria
     *
     * @return array
     */
    public function fetchAll($criteria = array()) {

        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
               ->from(array('m'=>'module'),array('m.*'));
        if(isset ($criteria['application_id'])){
            $select->where('m.application_id = ?', $criteria['application_id']);
        }
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $modules   = array();
        foreach ($resultSet as $row) {
            $module = new Application_Model_Module();
            $this->_rowToModule($row, $module);
            $modules[] = $module;
        }
        return $modules;
    }

    /**
     * Save/Update Module entity
     *
     * @param Application_Model_Module $module
     */
    public function save(Application_Model_Module $module) {
        $data = array(
            'id'                => $module->get_id(),
            'application_id'    => $module->get_application_id(),
            'code'              => $module->get_code(),
            'name'              => $module->get_name(),
            'description'       => $module->get_description(),
            'settings'          => $module->get_settings(),
            'data'              => $module->get_data()
        );
        $id = $module->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $moduleId = $this->_dbTable->insert($data);
            if($moduleId > 0){
                $module->set_id($moduleId);
                return true;
            }
            else{
                return false;
            }
        } else {
            $result  = $this->_dbTable->update($data, array('id = ?' => $id));
            return $result > 0;
        }
    }

}