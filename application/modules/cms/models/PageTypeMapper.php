<?php
/**
 * Page Type Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_PageTypeMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Cms_Model_PageTypeMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Cms_Model_DbTable_PageType
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_PageType();
    }

    /**
     * get instance
     *
     *
     * @return Cms_Model_PageTypeMapper
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
     * Find and populate entity by id
     *
     * @param string $id
     * @param Cms_Model_PageType $pageType
     * @return boolean
     */
    public function find($id, Cms_Model_PageType $pageType) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('t'=>'cms_page_type'),array('t.*'))
                ->where("t.id = ?", $id);
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $pageType);
        return true;
    }

    /**
     * Find and populate entity by code
     *
     * @param string $code
     * @param Cms_Model_PageType $pageType
     * @return boolean
     */
    public function findByCode($code, Cms_Model_PageType $pageType) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('t'=>'cms_page_type'),array('t.*'))
                ->where("t.code = ?", $code);
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $pageType);
        return true;
    }
    
    /**
     * Find and populate entity by module
     *
     * @param string $module
     * @return array
     */
    public function findByModule($module) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('t'=>'cms_page_type'),array('t.*'))
                ->where("t.module = ?", $module);
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        $rows = array();
        if (0 == count($resultSet)) {
            return $rows;
        }
        
        foreach ($resultSet as $row) {
            $rows[] = $row->toArray();
        }
        return $rows;
    }

    /**
     * Find all page types
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('t'=>'cms_page_type'),array('t.*'));

        if(isset ($criteria['category_set_id'])){
            $select ->joinLeft(array('cpt'=>'cms_category_page_type'), 'cpt.type_id = t.id', array())
                    ->where('cpt.set_id = ?', $criteria['category_set_id']);
        }

        if(is_array($orderBy) && count($orderBy) > 0 ){
            $select->order($orderBy);
        }
        //echo $select->__toString();die();
        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        $pageTypes = array();
        if (0 == count($resultSet)) {
            return $pageTypes;
        }

        foreach ($resultSet as $row) {
            $pageType = new Cms_Model_PageType();
            $this->_rowToEntity($row->toArray(), $pageType);
            $pageTypes[] = $pageType;
        }

        return $pageTypes;
    }

    /**
     * Save entity
     *
     * @param Cms_Model_PageType $pageType
     * @param string $language
     */
    public function save(Cms_Model_PageType $pageType) {
        $data = array();

        $this->_entityToRow($pageType, $data);

        $id = $pageType->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $pageType->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
    }

    protected function _rowToEntity(array $row, Cms_Model_PageType $pageType){
        $row['data'] = $this->_getJsonData($row['data']);
        $pageType->setOptions($row);
    }

    protected function _entityToRow(Cms_Model_PageType $pageType,array &$row){
        $this->_populateDataArr($row, $pageType, array('id','code','name','description','module'), array('data'));
    }
    
    /**
     * Function to get page type by page type id 
     * 
     * @param int $pageTypeId
     * @return boolean|\Cms_Model_PageType 
     */
    public function getPageTypeByTypeId($pageTypeId) {
        $select = $this->_dbTable->select();
        $select->from(array('t'=>'cms_page_type'),array('t.*'));
        $select->where('t.id =?', $pageTypeId);
        
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $pageTypes = new Cms_Model_PageType($row->toArray());
        return $pageTypes;
    }
}