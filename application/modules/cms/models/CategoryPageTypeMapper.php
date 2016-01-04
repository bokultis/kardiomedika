<?php
/**
 * CategoryPageType Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Cms_Model_CategoryPageTypeMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Cms_Model_DbTable_CategoryPageType
     */
    protected static $_instance = null;

    /**
     *
     * @var Cms_Model_DbTable_CategoryPageType
     */
    protected $_dbTable;


    /**
     * Constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_CategoryPageType();
    }

    /**
     * Get instance
     * @return Cms_Model_CategoryPageTypeMapper
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
     * Find by category id
     *
     * @param string $category_id
     * @return boolean
     */
    public function fetchByCategory($category_id){
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('p'=>'cms_category_page_type'),array('p.*'))
                ->where("p.category_id = ?", $category_id);
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);

        $categoryPageTypes = array();
        if (0 == count($resultSet)) {
            return $categoryPageTypes;
        }

        foreach ($resultSet as $row) {
            $categoryPageTypes[] = new Cms_Model_CategoryPageType($row->toArray());
        }

        return $categoryPageTypes;
    }

    /**
     * Fetch Cms_Model_CategoryPageType
     * @param Cms_Model_CategoryPageType $categoryPageType
     * @return boolean
     */
    public function fetch($categoryPageType){
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('cms_category_page_type'))
                ->where('category_id = ?', $categoryPageType->get_category_id())
                ->where('set_id = ?', $categoryPageType->get_set_id())
                ->where('type_id = ?', $categoryPageType->get_type_id());

        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $categoryPageType = new Cms_Model_CategoryPageType($row->toArray());
        return $categoryPageType;
    }


    /**
     * Save entity
     *
     * @param Cms_Model_CategoryPageType $categoryPageType
     */
    public function save(Cms_Model_CategoryPageType $categoryPageType) {

        $data = array();
        $this->_populateDataArr($data, $categoryPageType, array('id', 'set_id', 'type_id', 'category_id'));
        $id = $categoryPageType->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $categoryPageType->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
    }

    /**
     * Save entity
     *
     * @param Cms_Model_CategoryPageType $categoryPageType
     */
    public function delete($set_id, $category_id) {
        //delete page
        $this->_dbTable->delete(array(
            'set_id = ?'   => $set_id,
            'category_id = ?'   => $category_id
        ));
    }

}