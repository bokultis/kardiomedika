<?php
/**
 * Category Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Cms_Model_CategoryMapper extends HCMS_Model_Mapper {

    /**
     * Singleton instance
     *
     * @var Cms_Model_CategoryMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Cms_Model_DbTable_Category
     */
    protected $_dbTable;

    /**
     *
     * @var array
     */
    protected static $_translatedFields = array('url_id','name','description','meta');

    /**
     * Constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_Category();
    }

    /**
     * Get instance
     *
     * @return Cms_Model_CategoryMapper
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
     * Row to entity
     *
     * @param array $row
     * @param Cms_Model_MenuItem $category
     */
    protected function _rowToEntity(array $row, Cms_Model_Category $category){
        $row = $this->_makeTranslationData($row, self::$_translatedFields,array('data','meta'));
        $category->setOptions($row);
    }

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Cms_Model_Category $category
     * @return boolean
     */
    public function find($id, Cms_Model_Category $category, $language = null) {
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('c'=>'cms_category'),array('c.*'))
                ->where("c.id = ?", $id);

        if(isset ($language)){
            $this->_makeTranslationJoin($language, $select, 'cms_category', 'c', 'id', self::$_translatedFields);
        }

        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $category);
        return true;
    }

    /**
     * Find by URL ID
     *
     * @param string $urlId
     * @param Cms_Model_Category $category
     * @return boolean
     */
    public function findByUrlId($urlId, Cms_Model_Category $category, $language = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('c'=>'cms_category'),array('c.*'));
        if(isset ($language)){
            $this->_makeTranslationJoin($language, $select, 'cms_category', 'c', 'id', self::$_translatedFields);
            $select->where("(ct.url_id = ? OR c.url_id = ?)",$urlId);
        }
        else{
            $select->where("c.url_id = ?", $urlId);
        }
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $category);
        return true;
    }

    /**
     * Find all items
     * @param array $criteria
     * @param array $order
     * @param array $paging
     * @return array
     */
    public function fetchAll($criteria = array(), $order = array(), &$paging = null) {//print_r($criteria[typeCode]); exit;
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
            ->from(array('c'=>'cms_category'),array('c.*'))
            ->joinLeft(array('cs' => 'cms_category_set'), "cs.id = c.set_id",  array())
            ->joinInner(array('pt' => 'cms_page_type'), "cs.page_type_id = pt.id",  array());
        if( isset ($criteria['set_id']) ){
            $select->where('c.set_id = ?', $criteria['set_id']);
        }
        if( isset ($criteria['type_id']) ){
            $select->where('cs.page_type_id = ?', $criteria['type_id']);
        }
        if( isset ($criteria['type_code']) ){
            $select->where('pt.code = ?', $criteria['type_code']);
        }

        if(isset ($criteria['lang'])){
            $this->_makeTranslationJoin($criteria['lang'], $select, 'cms_category', 'c', 'id', self::$_translatedFields);
        }

        if( isset ($criteria['category']) && $criteria['category'] != '' ){
            $select->where('cs.name = ?', $criteria['category']);
        }
        
        if( isset ($criteria['category_id']) && $criteria['category_id'] != '' ){
            $select->where('c.id = ?', $criteria['category_id']);
        }
        if( isset ($criteria['set_id']) && $criteria['set_id'] != '' ){
            $select->where('c.set_id = ?', $criteria['set_id']);
        }

        if( isset ($criteria['page_id'])){
            $select->joinLeft(array('cp' => 'cms_category_page'), "cp.category_id = c.id",  array());
            $select->where('cp.page_id = ?', $criteria['page_id']);
        }
        else if( isset ($criteria['page_ids']) && is_array($criteria['page_ids']) && count($criteria['page_ids'])){
            $select->joinLeft(array('cp' => 'cms_category_page'), "cp.category_id = c.id",  array('page_id' => 'cp.page_id'));
            $select->where('cp.page_id IN (?)', $criteria['page_ids']);
        }

        if( is_array($order) && count($order) ){
            $select->order($order);
        }

        //echo $select->__toString();die();
        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        if (0 == count($resultSet)) {
            return false;
        }
        $categories   = array();
        foreach ($resultSet as $row) {
            $category = new Cms_Model_Category();
            $this->_rowToEntity($row->toArray(), $category);
            if( !isset ($criteria['group_by_page'])){
                $categories[] = $category;
            }else{
                if(!isset ($categories[$row->page_id])){
                    $categories[$row->page_id] = array();
                }
                $categories[$row->page_id][] = $category;
            }
            
        }
        return $categories;
    }

    /**
     * Get Zend Navigation categories
     *
     * @param array $criteria
     * @return array
     */
    public function fetchZendNavigationArray($criteria = array(), $order = array()){
        
        $categories = $this->fetchAll($criteria, $order);
        if(!is_array($categories)){
            return array();
        }
        /**
         * Tree structure is plain array where key is item ID from db table.
         * Every key has data like assocc array with all item data + array child_nodes where are ID's of all child nodes of item
         * There is only one string key called 'root', it points to ID of root of the tree, if it is 0 then there is no tree.
         * This representation of tree is good because it is filled with one pass by reading db and it can be processed recursively
         */
        $tree = array('root' => 0, 0 => array('child_nodes' => array()));
        /*@var $category Cms_Model_Category */
        foreach ($categories as $category) {
            // if there is no parent node of current node then it is out of order, we can only skip it
            if (!isset($tree[$category->get_parent_id()])) {
                continue;
            }
            // add current node in the list of parent's children
            $tree[$category->get_parent_id()]['child_nodes'][] = $category->get_id();
            // add current note in tree
            $tree[$category->get_id()]['entity'] = $category;
            // make empty children list for current node
            $tree[$category->get_id()]['child_nodes'] = array();
        }
        $links = $this->_generatePagesTree($tree, 0);
//        print_r($categories);die;
        return $links;
    }
    
    /**
     * Generate Zend_Navigation expected array of pages tree
     *
     * @param array $tree
     * @param int $id
     * @return array
     */
    private function _generatePagesTree($tree, $id) {
        $item = array();
        if ($id == $tree['root']) {
            foreach ($tree[$id]['child_nodes'] as $key => $val) {
                $item[] = $this->_generatePagesTree($tree, $val);
            }
        } else {
            /*@var $entity Cms_Model_Category */
            $entity = $tree[$id]['entity'];
            $item =  array(
                'id'           => $entity->get_id(),
                'set_id'       => $entity->get_set_id(),
                'name'         => $entity->get_name(),
                'description'  => $entity->get_description(),
                'parent_id'    => $entity->get_parent_id(),
                'level'        => $entity->get_level(),
                'data'         => $entity->get_data(),
            );
            // after node is done, process it's children
            foreach ($tree[$id]['child_nodes'] as $key => $val) {
                $item['pages'][] = $this->_generatePagesTree($tree, $val);
            }
        }
        return $item;
    }

    /**
     * Save entity
     *
     * @param Cms_Model_Category $category
     */
    public function save(Cms_Model_Category $category, $language = null) {
        $data = array();
        $this->_entityToRow($category, $data);

        $id = $category->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $category->set_id($id);
        } else {
            //if language is defined, save just untraslated strings
            if($language && $language != HCMS_Utils::getDefaultLocale()){
                foreach (self::$_translatedFields as $field) {
                    unset($data[$field]);
                }
            }
            $this->_dbTable->update($data, array('id = ?' => $id));
        }

        if($language){
            $this->_saveTranslation('cms_category', $category->get_id(), $category, self::$_translatedFields, $language, array('meta'));
        }
    }
    
    /**
     * Delete Category
     *
     * @param int $ids
     */
    public function delete($id){
        //delete category link with page
        $this->_dbTable->getAdapter()->delete('cms_category_page', array(
            'category_id = ?'   => $id
        ));

        //delete page
        $result = $this->_dbTable->getAdapter()->delete('cms_category', array(
            'id = ?'   => $id
        ));
        
        return ($result > 0);
    }
    
    /**
     * Entity to row
     *
     * @param Cms_Model_Category $category
     * @param array $row
     */
    protected function _entityToRow(Cms_Model_Category $category, array &$row){
        $this->_populateDataArr($row, $category, array('id','url_id','set_id','name',
            'description','parent_id','level','data','meta'), array('data','meta'));
        $row['level'] = $this->getLevel($row['parent_id']);
    }

    /**
     * Get level
     * 
     * @param int $parentId
     * @return int
     */
    protected function getLevel($parentId = 0){
        if($parentId > 0){
            $select = $this->_dbTable->select();
            $select ->setIntegrityCheck(false)
                    ->from(array('c'=>'cms_category'),array('c.level'))
                    ->where("c.id = ?", $parentId);
            $resultSet = $this->_dbTable->fetchAll($select);
            if (0 == count($resultSet)) {
                return false;
            }
            $row = $resultSet->current()->toArray();
            return (int)$row['level'] + 1;
        }else{
            return 0;
        }
    }

}