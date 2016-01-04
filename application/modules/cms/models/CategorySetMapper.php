<?php
/**
 * CategorySet Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Cms_Model_CategorySetMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Cms_Model_CategorySetMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Cms_Model_DbTable_CategorySet
     */
    protected $_dbTable;


    /**
     * Constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_CategorySet();
    }

    /**
     * Get instance
     * @return Cms_Model_CategorySetMapper
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
     * Get CategorySets
     * @return array
     */
    public function getCategorySets(){
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('cs'=>'cms_category_set'),array('cs.*'));
        $select->order(array("cs.name DESC"));
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $categorySets = array();
        foreach ($resultSet as $row) {
            $categorySets[] = $row->toArray();
        }
        return $categorySets;
    }


    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Cms_Model_CategorySet $categorySet
     * @return boolean
     */
    public function find($id, Cms_Model_CategorySet $categorySet) {
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('cs'=>'cms_category_set'),array('cs.*'))
                ->where("cs.id = ?", $id);

        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $categorySet->setOptions($row->toArray());
        return true;
    }

    /**
     * Save entity
     *
     * @param Cms_Model_CategorySet $categorySet
     */
    public function save(Cms_Model_CategorySet $categorySet) {
        $data = array();
        $this->_populateDataArr($data, $categorySet, array('id', 'name', 'description', 'module', 'page_type_id'));
        $id = $categorySet->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $categorySet->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
    }
}