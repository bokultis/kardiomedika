<?php
/**
 * Menu Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Cms_Model_MenuMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Cms_Model_MenuMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Cms_Model_DbTable_Menu
     */
    protected $_dbTable;


    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_Menu();
    }

    /**
     * get instance
     *
     *
     * @return Cms_Model_MenuMapper
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
     * Get Menus
     * @return array
     */
    public function getMenus(){
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('m'=>'cms_menu'),array('m.*'));
        $select->order(array("m.code DESC"));
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $menus = array();
        foreach ($resultSet as $row) {
            $menus[] = $row->toArray();
        }
        return $menus;
    }
    
    /**
     * Save entity
     *
     * @param Cms_Model_Menu $page
     * @param string $language
     */
    public function save(Cms_Model_Menu $menu) {
        $data = array();
        $this->_populateDataArr($data, $menu, array('id','code','name'));
        $id = $menu->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $menu->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }        
    }
}