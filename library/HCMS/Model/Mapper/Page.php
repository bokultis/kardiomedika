<?php
/**
 * Page Mapper
 *
 * @package HCMS
 * @subpackage Model
 * @copyright Horisen
 * @author milan
 */
class HCMS_Model_Mapper_Page extends HCMS_Model_Mapper{

    /**
     *
     * @var Cms_Model_DbTable_Page
     */
    protected $_dbTable;
   
    protected $_translatedFields = array('title','content','teaser','data','meta','url_id');

    /**
     * private constructor
     */
    protected function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_Page();
    }
    
    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Cms_Model_Page $page
     * @return boolean
     */
    public function find($id, Cms_Model_Page $page, $language = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('p'=>'cms_page'),array('p.*'))
                ->where("p.id = ?", $id);
        if(isset ($language)){
            $this->_makeTranslationJoin($language, $select, 'cms_page', 'p', 'id', $this->_translatedFields);
        }
        $this->_joinExtension($select,array('lang'=>$language));
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $page);
        return true;
    }

    /**
     * Join extension hook
     * 
     * @param Zend_Db_Select $select
     */
    protected function _joinExtension(Zend_Db_Select $select,$criteria = array()){
        
    }

    /**
     * Find by code
     *
     * @param string $code
     * @param int $applicationId
     * @param Cms_Model_CmsPage $page
     * @return boolean
     */
    public function findByCode($code, $applicationId, Cms_Model_Page $page, $language = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('p'=>'cms_page'),array('p.*'))
                ->where("p.application_id = ?", $applicationId)
                ->where("p.code = ?", $code);
        if(isset ($language)){
            $this->_makeTranslationJoin($language, $select, 'cms_page', 'p', 'id', $this->_translatedFields);
        }
        $this->_joinExtension($select,array('lang'=>$language));
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $page);
        return true;
    }

    /**
     * Find by URL ID
     *
     * @param string $urlId
     * @param int $applicationId
     * @param Cms_Model_CmsPage $page
     * @return boolean
     */
    public function findByUrlId($urlId, $applicationId, Cms_Model_Page $page, $language = null, $typeCode = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('p'=>'cms_page'),array('p.*'))
                ->where("p.application_id = ?", $applicationId);
        
        if(isset ($typeCode)){
            $select ->joinLeft(array('t'=>'cms_page_type'), 't.id = p.type_id', array('type_name'=>'t.name'))
                    ->where('t.code = ?', $typeCode);
        }
        if(isset ($language)){
            $this->_makeTranslationJoin($language, $select, 'cms_page', 'p', 'id', $this->_translatedFields);
            $select->where("(pt.url_id = ? OR p.url_id = ?)",$urlId);
        }
        else{
            $select->where("p.url_id = ?", $urlId);
        }
        $this->_joinExtension($select,array('lang'=>$language));
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $page);
        return true;
    }

    /**
     * Find all pages
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('p'=>'cms_page'),array('p.*'))
                ->joinLeft(array('t'=>'cms_page_type'), 't.id = p.type_id', array('type_name'=>'t.name'))
                ->joinLeft(array('u'=>'auth_user'), 'u.id = p.user_id', array('user_name'=>'u.username'));

        if(isset ($criteria['application_id'])){
            $select->where('p.application_id = ?', $criteria['application_id']);
        }

        if(isset ($criteria['type_id'])){
            $select->where('p.type_id = ?', $criteria['type_id']);
        }

        if(isset ($criteria['type_code'])){
            $select->where('t.code = ?', $criteria['type_code']);
        }

        if(isset ($criteria['status'])){
            $select->where('p.status = ?', $criteria['status']);
        }

        if(isset ($criteria['user_id'])){
            $select->where('p.user_id = ?', $criteria['user_id']);
        }

        if(isset ($criteria['category_id'])){
            $select ->joinLeft(array('cp'=>'cms_category_page'), 'cp.page_id = p.id', array())
                    ->where('cp.category_id = ?', $criteria['category_id']);
        }

        if(isset ($criteria['menu_item_id'])){
            $select ->distinct()
                    ->joinLeft(array('cmi'=>'cms_menu_item'), 'cmi.page_id = p.id', array())
                    ->joinLeft(array('cmip'=>'cms_menu_item'), 'cmip.page_id = p.id', array())
                    ->where('cmi.id = ? OR cmip.parent_id = ?', $criteria['menu_item_id']);
        }        

        if(isset ($criteria['search_filter'])){
            $searchString =  '%' . $criteria['search_filter'] . '%';
            if(isset ($criteria['lang'])){
                $select->where('(pt.title LIKE ? OR p.title LIKE ? OR p.code LIKE ?)',$searchString);
            }
            else{
                $select->where('(p.title LIKE ? OR p.code LIKE ?)',$searchString);
            }
        }

        if(isset ($criteria['lang'])){
            $this->_makeTranslationJoin($criteria['lang'], $select, 'cms_page', 'p', 'id', $this->_translatedFields);
        }
        $this->_joinExtension($select,$criteria);
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

        $pages = array();
        if (0 == count($resultSet)) {
            return $pages;
        }

        foreach ($resultSet as $row) {
            $page = new Cms_Model_Page();
            $this->_rowToEntity($row->toArray(), $page);
            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * Save entity
     *
     * @param Cms_Model_Page $page
     * @param string $language
     */
    public function save(Cms_Model_Page $page, $language = null) {
        $data = array();
           
        $this->_entityToRow($page, $data);
        unset($data['extension']);
        $id = $page->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $page->set_id($id);
        } else {
            //if language is defined and curr lang is default one - save all in default table, else save just untraslated strings
            //echo "curr lang: " . $language . ", def lang: " . HCMS_Utils::getDefaultLocale();
            if($language && $language != HCMS_Utils::getDefaultLocale()){
                foreach ($this->_translatedFields as $field) {
                    unset($data[$field]);
                }
            }
//            print_r($data);die;
            if(!isset($data['code']))$data['code'] = "";
            if($language && $language == HCMS_Utils::getDefaultLocale()){
                if(!isset($data['teaser']))$data['teaser'] = "";
                if(!isset($data['content']))$data['content'] = "";
            }
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
        //save in translation table
        if($language){
            $this->_saveTranslation('cms_page', $page->get_id(), $page, $this->_translatedFields, $language, array('data','meta'));
        }
        //save extension
        $this->_saveExtension($page, $language);
    }

    /**
     * Save data in _tr table
     *
     * @param string $tableName
     * @param int $entityId
     * @param HCMS_Model_Entity $object
     * @param array $fields
     * @param string $language
     * @param array $jsonFields
     * @return int
     */
    protected function _saveTranslation($tableName, $entityId, HCMS_Model_Entity $object, $fields, $language, $jsonFields = array()){
        $trTableName = $tableName . "_tr";
        $data = array();
        $this->_populateDataArr($data, $object, $fields, $jsonFields);
        $select = $this->_dbTable->getAdapter()->select();
        $select ->from($trTableName)
                ->where("translation_id = ?",$entityId)
                ->where("language = ?",$language);
        $resultSet = $this->_dbTable->getAdapter()->fetchAll($select);
        if (0 == count($resultSet)) {
            $data['translation_id'] = $entityId;
            $data['language'] = $language;
            $id = $this->_dbTable->getAdapter()->insert($trTableName,$data);
            return 1;
        }
        else{
            if(!isset($data['teaser']))$data['teaser'] = "";
            if(!isset($data['content']))$data['content'] = "";
            return $this->_dbTable->getAdapter()->update($trTableName, $data,
                    array(
                        'translation_id = ?' => $entityId,
                        'language = ?'      => $language
                    ));
        }

    }

    /**
     * Hook to save extension
     * 
     * @param Cms_Model_Page $page
     * @param string $language
     */
    protected function _saveExtension(Cms_Model_Page $page, $language = null){
        
    }

    /**
     * Hook to convert array to extension entity
     * 
     * @param array $row
     * @return mixed
     */
    protected function _rowToExtension(array $row){
        return null;
    }

    protected function _rowToEntity(array $row, Cms_Model_Page $page){
        $row = $this->_makeTranslationData($row, $this->_translatedFields, array('data','meta'));
        $page->setOptions($row);
        //set extension
        $page->set_extension($this->_rowToExtension($row));
    }

    /**
     * Hook to attach extension object to row array
     * 
     * @param array $row
     */
    protected function _extensionToRow(Cms_Model_Page $page, array &$row){
        
    }

    protected function _entityToRow(Cms_Model_Page $page,array &$row){
        $this->_populateDataArr($row, $page, array('id','code','url_id','application_id',
                'type_id','user_id','posted','format','title','content','status',
                'teaser','data','meta'),array('data','meta'));
        $this->_extensionToRow($page, $row);
    }

    /**
     * Save page categories
     *
     * @param Cms_Model_Page $page
     * @param array $categoryIds
     */
    public function saveCategories(Cms_Model_Page $page, $categoryIds) {
        //reset existing categories
        $this->_dbTable->getAdapter()->delete('cms_category_page',
                array('page_id = ?' => $page->get_id())
        );
        if(!$categoryIds || !is_array($categoryIds) || count($categoryIds) == 0){
            return false;
        }

        foreach ($categoryIds as $categoryId) {
            $this->_dbTable->getAdapter()->insert('cms_category_page', array(
                'page_id'       => $page->get_id(),
                'category_id'   => $categoryId
            ));
        }
    }

    /**
     * Fetch page category ids
     *
     * @param Cms_Model_Page $page
     * @return array
     */
    public function fetchCategories(Cms_Model_Page $page) {
        $categoryIds = array();
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('c'=>'cms_category_page'),array('c.*'))
                ->where('c.page_id = ?',$page->get_id());
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return $categoryIds;
        }

        foreach ($resultSet as $row) {
            $page = new Cms_Model_Page();
            $categoryIds[] = $row->category_id;
        }
        return $categoryIds;
    }

    /**
     * Hook to delete extension
     *
     * @param Cms_Model_Page $page
     */
    protected function _deleteExtension(Cms_Model_Page $page){

    }


    /**
     * Check if the page is in use in menus, routes...
     *
     * @param Cms_Model_Page $page
     * @return boolean
     */
    public function isInUse(Cms_Model_Page $page){
        //check menu items
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('mi'=>'cms_menu_item'),array('mi.id'))
                ->where('mi.page_id = ?',$page->get_id());
        //echo $select->__toString();die();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (count($resultSet)) {
            return true;
        }
        //check routes
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('r'=>'cms_route'),array('r.id'))
                ->where('r.page_id = ?',$page->get_id());
        //echo $select->__toString(); die();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (count($resultSet)) {
            return true;
        }
        return false;
    }

    /**
     * Delete Page
     *
     * @param int $ids
     */
    public function delete(Cms_Model_Page $page){
        //delete extension
        $this->_deleteExtension($page);
        //delete page categories
        $this->_dbTable->getAdapter()->delete('cms_category_page', array(
            'page_id = ?'   => $page->get_id()
        ));
        //delete page translations
        $this->_dbTable->getAdapter()->delete('cms_page_tr', array(
            'translation_id = ?'   => $page->get_id()
        ));
        //delete page
        $result = $this->_dbTable->getAdapter()->delete('cms_page', array(
            'id = ?'   => $page->get_id()
        ));
        
        return ($result > 0);
    }
}
