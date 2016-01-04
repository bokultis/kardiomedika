<?php
/**
 * Translation Language Model Mapper
 *
 * @package Translation
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Translation_Model_LangMapper extends HCMS_Model_Mapper
{
     /**
     * singleton instance
     *
     * @var Translation_Model_LangMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Translation_Model_DbTable_Lang
     */
    protected $_dbTable;


    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Translation_Model_DbTable_Lang();
        $this->_initLogger();
    }

    /**
     * get instance
     *
     *
     * @return Translation_Model_LangMapper
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
     * Find all types
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

        $languages = array();
        if (0 == count($resultSet)) {
            return $languages;
        }

        foreach ($resultSet as $row) {
            $rowArray = $row->toArray();
            $language = new Translation_Model_Lang($rowArray);
            $languages[] = $language;
        }

        return $languages;
    }
    
    /**
     * Save entity
     *
     * @param Translation_Model_Lang $lang
     */
    public function save(Translation_Model_Lang $lang) {
        $data = array();
      
        $data = $lang->toArray();

        $id = $lang->get_id();
        
        $selected_default_lang_id = Translation_Model_LangMapper::getInstance()->findByDefaultValue('yes');
        if($selected_default_lang_id == $id && $values['default'] != 'yes'){
            $data['default'] = 'yes';
        }
        
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $lang->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
        /**
         * update default language 
         */
        if($data['default'] == 'yes'){
            $this->_dbTable->update(array('default' => 'no'), array('id != ?' => $lang->get_id()));
        }

    }
    
     /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Translation_Model_Lang $lang
     * @return boolean
     */
    public function find($id, Translation_Model_Lang $lang) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->where("id = ?", $id);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $rowArray = $row->toArray();
        $lang->setOptions($rowArray);
        return true;
    }
    
     /**
     * Find and populate entity by code
     *
     * @param string $code
     * @param Translation_Model_Lang $lang
     * @return boolean
     */
    public function findByCode($code, Translation_Model_Lang $lang) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->where("code = ?", $code);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $rowArray = $row->toArray();
        $lang->setOptions($rowArray);
        return true;
    }
    
         
    /**
     * Find and populate entity by default value
     *
     * @param string $value    
     * @return boolean/id
     */
    public function findByDefaultValue($value) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->where("`default` = ?", $value);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }

        $row = $resultSet->current();
        $rowArray = $row->toArray();
     
        return $rowArray['id'];
    }


    /**
     * Delete data
     *
     * @param int $id
     * @return int|bool
     */
    public function delete(Translation_Model_Lang $lang){
        
        $result = $this->_dbTable->getAdapter()->delete('translate_language', array(
            'id = ?'   => $lang->get_id()
        ));
        
        return ($result > 0);
        
    }
}
