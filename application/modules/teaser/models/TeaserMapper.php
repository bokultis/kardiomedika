<?php
/**
 * Teaser Mapper
 *
 * @package Teaser
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Teaser_Model_TeaserMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Teaser_Model_TeaserMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Teaser_Model_DbTable_Teaser
     */
    protected $_dbTable;
    
    protected static $_boxes = null;
    
    /**
     * Cached teasers
     * 
     * @var array 
     */
    protected $_activeTeasers = array();    

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Teaser_Model_DbTable_Teaser();
        $this->_initLogger();
    }

    /**
     * get instance
     * 
     * @return Teaser_Model_TeaserMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }   
    
    /**
     * Get box configuration
     * @param string $boxCode
     * @return array|null
     */
    public function getBox($boxCode = null)
    {
        if(self::$_boxes === null){
            //self::$_boxes = json_decode(file_get_contents($filePath), true);
            $boxes = HCMS_Utils::loadThemeConfig('boxes.php', 'teaser');
            foreach ($boxes as $code => $box) {
                if(isset($boxes[$code]['params']['images_dims'])){
                    $section = isset($boxes[$code]['params']['images_section'])? $boxes[$code]['params']['images_section']: 'default';                    
                    $boxes[$code]['params']['images'] = $this->getImagesParams($boxes[$code]['params']['images_dims'], $section);                    
                }
            }
            self::$_boxes = $boxes;
        }
        if(isset($boxCode)){
            if(isset(self::$_boxes[$boxCode])){
                return self::$_boxes[$boxCode];
            }
            else{
                return null;
            }
        }
        return self::$_boxes;
    }
    
    /**
    * Get image params
    * 
    * @param array $dims
    * @param boolean $independent960
    * @return array
    */
    public function getImagesParams($dims, $section = 'default'){
        $imagesConf = HCMS_Utils::loadThemeConfig('picture.php');
        $variations = $imagesConf[$section];

        $result = array();
        $i = 0;
        foreach ($variations as $query => $suffix) {
            $width = $dims[$i * 2];
            $height = $dims[$i * 2 + 1];
            $elements = explode('_', $suffix);
            $vp = $elements[0];
            $density = (count($elements) >= 2)? $elements[1]: 1;
            $result['img_' . $suffix] = array(
                "name" => "Image for viewport width $vp and density $density",
                "media_query" => $query,
                "options" => array(
                    "minwidth" => $width,
                    "maxwidth" => $width,
                    "minheight" => $height,
                    "maxheight" => $height
                )
            );
            $i++;
        }

        return $result;

    }    
    
     /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Teaser_Model_Teaser $teaser
     * @return boolean
     */
    public function find($id, Teaser_Model_Teaser $teaser) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->where("id = ?", $id);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $teaser);
        return true;
    }        


    /**
     * Find all teasers
     * 
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('t'=>'teaser'),array('t.*'));
   
        if(isset ($criteria['search_filter'])){
            $select->where('t.name LIKE ? ','%' . $criteria['search_filter'] . '%');
        }
        if(isset ($criteria['box_code'])){
            $select->where('t.box_code = ? ',$criteria['box_code']);
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

        $teasers = array();
        if (0 == count($resultSet)) {
            return $teasers;
        }

        foreach ($resultSet as $row) {
            $teaser = new Teaser_Model_Teaser();
            $this->_rowToEntity($row->toArray(), $teaser);
            $teasers[] = $teaser;
        }

        return $teasers;
    }
    
    /**
     * Find all items
     * 
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchWithItems($criteria = array(), $orderBy = array(), &$paging = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('t'=>'teaser'),array('teaser_id' => 't.id', 'teaser_name' => 't.name', 'teaser_box_code' => 't.box_code', 'teaser_content' => 't.content', 'teaser_all_menu_items' => 'all_menu_items'))
                ->joinLeft(array('thi'=>'teaser_has_items'), 'thi.teaser_id = t.id', array('order_num'=>'thi.order_num'))
                ->joinLeft(array('i'=>'teaser_item'), 'thi.item_id = i.id', array('i.*'));
   
        if(isset ($criteria['lang'])){
            $this->_makeTranslationJoin($criteria['lang'], $select, 'teaser_item', 'i', 'id', Teaser_Model_ItemMapper::$_translatedFields);
        }
        if(isset ($criteria['search_filter'])){
            $select->where('i.title LIKE  ?','%' . $criteria['search_filter'] . '%');
        }
        
        if(isset ($criteria['title'])){
            $select->where('i.title LIKE ? ','%' . $criteria['title'] . '%');
        }
        
        if(isset ($criteria['name'])){
            $select->where('t.name LIKE ? ','%' . $criteria['name'] . '%');
        }        
        
        if(isset ($criteria['preview_teaser_id'])){
            $select->where('t.id = ? ', $criteria['preview_teaser_id']);
        }        
        
        if(isset ($criteria['start_dt'])){
            $select->where('i.start_dt >= ? ', HCMS_Utils_Date::dateLocalToIso($criteria['start_dt']));
        }
        
        if(isset ($criteria['end_dt'])){
            $select->where('i.end_dt <= ? ', HCMS_Utils_Date::dateLocalToIso($criteria['end_dt']));
        }
        
        if(isset ($criteria['fallback'])){
            $select->where('i.fallback = ? ', $criteria['fallback']);
        }

        if(isset ($criteria['box_code'])){
            $select->where('t.box_code = ? ', $criteria['box_code']);
        }
        
        if(isset ($criteria['menu_item_id'])){
            $select->joinLeft(array('tmi'=>'teaser_menu_item'), 'tmi.teaser_id = t.id', array());
            $select->where("(t.all_menu_items = 'yes' OR tmi.menu_item_id = ?)", $criteria['menu_item_id']);
        }        
        
        if(isset ($criteria['active']) && $criteria['active'] == 'yes'){
            $momentStr = isset($criteria['preview_dt'])? "'" . $criteria['preview_dt'] . "'" : 'NOW()';
            $select->where('i.fallback = \'yes\' OR (i.end_dt > ' . $momentStr . ' AND i.start_dt <= ' . $momentStr . ')');
            $select->where('i.start_dt <= ' . $momentStr);
        }   
        
        if(is_array($orderBy) && count($orderBy) > 0 ){
            $select->order($orderBy);
        }
        else{
            $select->order(array('t.id','thi.order_num'));
        }
        //echo $select->__toString();die();
        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        $teasers = array();
        if (0 == count($resultSet)) {
            return $teasers;
        }
        
        $itemMapper = Teaser_Model_ItemMapper::getInstance();
        $currTeaserId = -1;
        $teaser = null;
        $items = array();
        foreach ($resultSet as $row) {
            //new teaser
            if($row->teaser_id != $currTeaserId){
                if(isset($teaser)){
                    $teaser->set_items($items);
                }
                //reset items
                $items = array();
                $currTeaserId = $row->teaser_id;
                $teaser = new Teaser_Model_Teaser();
                $this->_rowToEntity(array(
                        'id'        => $row->teaser_id,
                        'name'      => $row->teaser_name,
                        'box_code'  => $row->teaser_box_code,
                        'content'   => $row->teaser_content,
                        'all_menu_items' => $row->teaser_all_menu_items
                    ), $teaser);
                $teasers[] = $teaser;
            }
            //items
            if($row->id){ //check if item exists
                $item = new Teaser_Model_Item();
                $itemArr = $row->toArray();
                $itemMapper->_rowToEntity($itemArr, $item);
                $items[] = $item;                
            }
        }
        if(isset($teaser) && count($items)){
            $teaser->set_items($items);
        }        

        return $teasers;
    }
    
    /**
     * Get active teasers for a lang
     * 
     * @param string $lang
     * @param int $menuItemId     
     * @param string $code box code
     * @param boolean $refresh
     * @return array 
     */
    public function getActiveTeasers($lang, $menuItemId, $code, $refresh = false){
        //cache all for lang/menu id
        if($refresh || !isset($this->_activeTeasers[$lang][$menuItemId])){            
            $this->_activeTeasers[$lang][$menuItemId] = $this->fetchWithItems(array(
                'active'        => true,
                'lang'          => $lang,
                'menu_item_id'  => $menuItemId
            ));
        }
        
        if(isset($this->_activeTeasers[$lang][$menuItemId])){
            if(!$code){
                return $this->_activeTeasers[$lang][$menuItemId];
            }
            $result = array();
            /* @var $teaser Teaser_Model_Teaser */
            foreach ($this->_activeTeasers[$lang][$menuItemId] as $teaser) {
                if($teaser->get_box_code() == $code){
                    $result[] = $teaser;
                }
            }
            return $result;
        }
        else{
            return array();
        }
    }    
    
    protected function _rowToEntity(array $row, Teaser_Model_Teaser $teaser) {
        $row = $this->_makeTranslationData($row, array(), array('content'));
        $teaser->setOptions($row);
    }

    protected function _entityToRow(Teaser_Model_Teaser $teaser, array &$row) {
        $this->_populateDataArr($row, $teaser, array('id', 'box_code', 'name', 'all_menu_items', 'content'), array('content'));
    }

    /**
     * Save entity
     *
     * @param Teaser_Model_Teaser $teaser
     */
    public function save(Teaser_Model_Teaser $teaser) {
        $data = array();
        
        $this->_entityToRow($teaser, $data);
        $id = $teaser->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $teaser->set_id($id);
             //add new relation with menu item
            $this->_saveMenuItemsids($teaser);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
            $this->_dbTable->getAdapter()->delete('teaser_menu_item',  array(
                'teaser_id = ?'   => $id
            ));
            //update new relation
            $this->_saveMenuItemsids($teaser);
        }        
    }
    /**
     * save relation menu item
     * @param Teaser_Model_Teaser $teaser 
     */
    protected function _saveMenuItemsids(Teaser_Model_Teaser $teaser){
        if(count ($menuItemIds = $teaser->get_menu_item_ids()) > 0){
            $menuItemId['teaser_id'] = $teaser->get_id();
            foreach ($menuItemIds as $value) {
                $menuItemId['menu_item_id']= $value;
                $this->_dbTable->getAdapter()->insert('teaser_menu_item', $menuItemId);
            }
        }
    }
    /**
     * Delete teaser
     * 
     * @param Teaser_Model_Teaser $teaser
     * @return boolean 
     */
    public function delete(Teaser_Model_Teaser $teaser){
        
        $result = $this->_dbTable->getAdapter()->delete('teaser', array(
            'id = ?'   => $teaser->get_id()
        ));
        return ($result > 0);
    }
    
    /**
     * Populate all menu ids where teaser belongs to
     * 
     * @param Teaser_Model_Teaser $teaser
     */
    public function populateMenuItemIds(Teaser_Model_Teaser $teaser) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('tmi'=>'teaser_menu_item'),array('tmi.*'))
                ->where('tmi.teaser_id = ?', $teaser->get_id());
                
        $resultSet = $this->_dbTable->fetchAll($select);

        $ids = array();
        if (0 == count($resultSet)) {
            $teaser->set_menu_item_ids(array());
            return;
        }
        foreach ($resultSet as $row) {
            $ids[] = $row->menu_item_id;
        }
        $teaser->set_menu_item_ids($ids);
    }
    
    
        /**
     * Populate all item where teaser belongs
     * 
     * @param Teaser_Model_Item $item
     */
    public function populateItem(Teaser_Model_Teaser $teaser) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('thi'=>'teaser_has_items'),array('thi.*'))
                ->where('thi.teaser_id = ?', $teaser->get_id());
                
        $resultSet = $this->_dbTable->fetchAll($select);

        $items = array();
        if (0 == count($resultSet)) {
            $teaser->set_items($items);
            return;
        }
        foreach ($resultSet as $row) {

            $item = new Teaser_Model_Item();
            Teaser_Model_ItemMapper::getInstance()->find($row->item_id, $item);
            $items []= $item;   
        }
        $teaser->set_items($items);
    }
    
    /**
     * Get current last order num for existing items
     * 
     * @param int $teaserId
     * @return int 
     */
    public function getMaxItemOrderNum($teaserId){
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('thi'=>'teaser_has_items'),array('max_order' => 'MAX(thi.order_num)'))
                ->joinLeft(array('ti'=>'teaser_item'), 'ti.id = thi.item_id', array())
                ->where('thi.teaser_id = ?', $teaserId)
                ->where("ti.fallback = 'no'");
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return 0;
        }
        $row = $resultSet->current();
        return ($row->max_order)? $row->max_order : 0;
    }
    
    /**
     * Set order number for an item
     * 
     * @param int $teaserId
     */
    public function setItemOrder($teaserId, $itemId, $orderNum){
        $this->_dbTable->getAdapter()->update('teaser_has_items', array(
            'order_num' => $orderNum
        ), array(
            'teaser_id = ?' => $teaserId,
            'item_id = ?'   => $itemId            
        ));
    }    
           
}