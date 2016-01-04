<?php
/**
 * Translation Menu Model Mapper
 *
 * @package Translation
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Translation_Model_MenuMapper extends HCMS_Model_Mapper
{
    /**
     * singleton instance
     *
     * @var Translation_Model_MenuMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Translation_Model_DbTable_Menu
     */
    protected $_dbTable;


    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Translation_Model_DbTable_Menu();
        $this->_initLogger();
    }

    /**
     * get instance
     *
     *
     * @return Auth_Model_RoleMapper
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
     * Find all menus
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
   
        if(isset ($criteria['search_filter'])){
            $searchString =  '%' . $criteria['search_filter'] . '%';            
            $select->where(' name LIKE ? ',$searchString);
        }
        
        if(isset ($criteria['name'])){
            $searchString =  '%' . $criteria['name'] . '%';            
            $select->where(' name LIKE ? ',$searchString);
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

        $types = array();
        if (0 == count($resultSet)) {
            return $types;
        }

        foreach ($resultSet as $row) {
            $rowArray = $row->toArray();
            $type = new Translation_Model_Menu($rowArray);
            $types[] = $type;
        }

        return $types;
    }
    
     /**
     * Save entity
     *
     * @param Translation_Model_Type $type
     */
    public function save(Translation_Model_Menu $menu) {
        $data = array();
      
        $data = $menu->toArray();
//        $this->_populateDataArr($data, $user, array('id','code','title',
//            'content','application_id'));

        $id = $menu->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $menu->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }        
    }
    
    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Translation_Model_Menu $menu
     * @return boolean
     */
    public function find($id, Translation_Model_Menu $menu) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->where("id = ?", $id);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $rowArray = $row->toArray();
        $menu->setOptions($rowArray);
        return true;
    }

    /**
     * Delete data
     *
     * @param int $id
     * @return int|bool
     */
    public function delete($del_id){
        $del_id = trim($del_id, ",");
        return $this->_dbTable->delete($this->_dbTable->getAdapter()->quoteInto("id IN (?)",explode(',', $del_id)));
    }

}
