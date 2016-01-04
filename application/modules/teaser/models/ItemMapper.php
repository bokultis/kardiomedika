<?php
/**
 * Item Mapper
 *
 * @package Teaser
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Teaser_Model_ItemMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Teaser_Model_ItemMapper
     */
    protected static $_instance = null;
    
    /**
     * Cached teasers
     * 
     * @var array 
     */
    protected $_activeTeasers = array();

    /**
     *
     * @var Teaser_Model_DbTable_Item
     */
    protected $_dbTable;

    public static $_translatedFields = array('title','content');

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Teaser_Model_DbTable_Item();
    }

    /**
     * get instance
     *
     *
     * @return Teaser_Model_ItemMapper
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
     * @param Teaser_Model_Item $item
     * @return boolean
     */
    public function find($id, Teaser_Model_Item $item, $language = null) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('ti'=>'teaser_item'),array('ti.*'))
                ->where("ti.id = ?", $id);
        if(isset ($language)){
            $this->_makeTranslationJoin($language, $select, 'teaser_item', 'ti', 'id', self::$_translatedFields);
        }
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $item);
        return true;
    }


    /**
     * Find all items
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)->from(array('i'=>'teaser_item'),array('i.*'));
        
        if(isset ($criteria['lang'])){
            $this->_makeTranslationJoin($criteria['lang'], $select, 'teaser_item', 'i', 'id', self::$_translatedFields);
        }
        if(isset ($criteria['search_filter'])){
            $select->where('i.title LIKE  ?','%' . $criteria['search_filter'] . '%');
        }
        
        if(isset ($criteria['title'])){
            $select->where(' i.title LIKE ? ','%' . $criteria['title'] . '%');
        }
        
        if(isset ($criteria['start_dt'])){
            $select->where(' i.start_dt >= ? ', HCMS_Utils_Date::dateLocalToIso($criteria['start_dt']));
        }
        
        if(isset ($criteria['end_dt'])){
            $select->where('i.end_dt <= ? ', HCMS_Utils_Date::dateLocalToIso($criteria['end_dt']));
        }
        
        if(isset ($criteria['fallback'])){
            $select->where('i.fallback = ? ', $criteria['fallback']);
        }
        if(isset ($criteria['box_id'])){           
            $select->where('i.box_id = ? ',$criteria['box_id']);
        }
        if(isset ($criteria['code'])){
            $select->where('tb.code = ? ', $criteria['code']);
        }
        if(isset ($criteria['active']) && $criteria['active'] == 'yes'){           
            $select->where('i.end_dt > NOW()');
            $select->where('i.start_dt <= NOW()');
        }
        if(isset ($criteria['box_code'])){
            $select->where('i.box_code = ? ', $criteria['box_code']);
        }        
        
        if(is_array($orderBy) && count($orderBy) > 0 ){
            $select->order($orderBy);
        }
        else{
            $select->order(array('i.order_num ASC'));
        }
//        echo $select->__toString();die();
        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        $items = array();
        if (0 == count($resultSet)) {
            return $items;
        }

        foreach ($resultSet as $row) {
            $item = new Teaser_Model_Item();
            $this->_rowToEntity($row->toArray(), $item);
            $items[] = $item;
        }

        return $items;
    }
    
    /**
     * Get active teasers for a lang
     * 
     * @param string $lang
     * @param boolean $refresh
     * @param string $code box code
     * @return array 
     */
    public function getActiveTeasers($lang, $refresh = false, $code = null){
        if($refresh || !isset($this->_activeTeasers[$lang])){
            $this->_activeTeasers[$lang] = $this->fetchAll(array(
                'active'    => 'yes',
                'lang'      => $lang
            ));
        }
        
        if(is_array($this->_activeTeasers[$lang])){
            if(!$code){
                return $this->_activeTeasers[$lang];
            }
            $result = array();
            /* @var $teaser Teaser_Model_Item */
            foreach ($this->_activeTeasers[$lang] as $teaser) {
                if($teaser->get_code() == $code){
                    $result[] = $teaser;
                }
            }
            return $result;
        }
        else{
            return array();
        }
    }
    
    /**
     * Get active teaser for code
     * 
     * @param string $code
     * @param string $lang
     * @param boolean $refresh
     * @return array|false
     */
    public function getActiveTeaser($code, $lang, $refresh = false){
        $activeTeasers = $this->getActiveTeasers($lang, $refresh);
        /* @var $teaser Teaser_Model_Item */
        foreach ($activeTeasers as $teaser) {
            if($teaser->get_code() == $code){
                return $teaser;
            }
        }
        
        return false;
    }    

    /**
     * Save entity
     *
     * @param Teaser_Model_Item $item
     * @param string $language
     */
    public function save(Teaser_Model_Item $item, $language = null) {
        $data = array();
        $this->_entityToRow($item, $data);
        if(!isset($data['item_template'])){
            $data['item_template'] = null;
        }
        if($data['fallback'] == 'yes'){
            $data['start_dt'] = '2014-10-01 00:00:00';
            $data['end_dt'] = '3000-01-01 00:00:00';
        }
        $id = $item->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $item->set_id($id);
        } else {
            //if language is defined and curr lang is default one - save all in default table, else save just untraslated strings
            if($language && $language != HCMS_Utils::getDefaultLocale()){
                foreach (self::$_translatedFields as $field) {
                    unset($data[$field]);
                }
            }
            $this->_dbTable->update($data, array('id = ?' => $id));
        }
         //save in translation table
        if($language){
            $this->_saveTranslation('teaser_item', $item->get_id(), $item, self::$_translatedFields, $language, array('content'));
        }
        $this->_saveTeaserIds($item);
    }


    
    public function _rowToEntity(array $row, Teaser_Model_Item $item){
        $row = $this->_makeTranslationData($row, self::$_translatedFields, array('content'));
        $item->setOptions($row);
    }

    public function _entityToRow(Teaser_Model_Item $item,array &$row){
        $this->_populateDataArr($row, $item, array('id','box_id','fallback',
            'start_dt','end_dt', 'title', 'content_type', 'content', 'order_num', 'box_code', 'item_template'),array('content'));
    }
 
    /**
     * Delete data
     *
     * @param int $id
     * @return int|bool
     */
    public function delete(Teaser_Model_Item $item){
        
        $result = $this->_dbTable->getAdapter()->delete('teaser_item', array(
            'id = ?'   => $item->get_id()
        ));
        $this->_dbTable->getAdapter()->delete('teaser_item_tr', array(
            'translation_id = ?'   => $item->get_id()
        ));
        //remove it from slides
        $this->_dbTable->getAdapter()->delete('teaser_has_items',  array('item_id = ?'   => $item->get_id()));
        return ($result > 0);
        
    }
    
    /**
     * Populate all ids where item belongs
     * 
     * @param Teaser_Model_Item $item
     */
    public function populateTeaserIds(Teaser_Model_Item $item) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('thi'=>'teaser_has_items'),array('thi.*'))
                ->where('thi.item_id = ?', $item->get_id());
                
        $resultSet = $this->_dbTable->fetchAll($select);

        $ids = array();
        if (0 == count($resultSet)) {
            $item->set_teaser_ids(array());
            return;
        }
        foreach ($resultSet as $row) {
            $ids[] = $row->teaser_id;
        }
        $item->set_teaser_ids($ids);
    }
    
    /**
     * Save relation teaser ids
     * 
     * @param Teaser_Model_Item $item 
     */
    protected function _saveTeaserIds(Teaser_Model_Item $item){
        //get existing teaser ids
        $oldItem = new Teaser_Model_Item();
        $oldTeaserIds = array();
        if($this->find($item->get_id(), $oldItem)){
            $this->populateTeaserIds($oldItem);
            $oldTeaserIds = $oldItem->get_teaser_ids();
        }
        //new ids
        $teaserIds = $item->get_teaser_ids();
        $data = array('item_id' => $item->get_id());
        foreach ($teaserIds as $teaserId) {
            //new teaser
            if(!in_array($teaserId, $oldTeaserIds)){
                $data['teaser_id'] = $teaserId;
                //put this item as last one
                if($item->get_fallback() == 'yes'){
                    $data['order_num'] = 100000;
                }
                else{
                    $data['order_num'] = Teaser_Model_TeaserMapper::getInstance()->getMaxItemOrderNum($teaserId) + 1;
                }                
                $this->_dbTable->getAdapter()->insert('teaser_has_items', $data);
            }
            else{
                $pos = array_search($teaserId, $oldTeaserIds);
                if($pos !== false){
                    unset($oldTeaserIds[$pos]);
                }
                elseif($item->get_fallback() == 'yes' && $oldItem->get_fallback() != $item->get_fallback()){
                    Teaser_Model_TeaserMapper::getInstance()->setItemOrder($teaserId, $item->get_id(), 100000);
                }
            }
        }
        //now delete pending old ids
        foreach ($oldTeaserIds as $oldTeaserId) {
            $this->_dbTable->getAdapter()->delete('teaser_has_items',  array('teaser_id = ?'   => $oldTeaserId, 'item_id = ?' => $item->get_id()));
        }
    }    

}