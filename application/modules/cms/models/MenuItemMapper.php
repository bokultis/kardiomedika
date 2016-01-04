<?php
/**
 * MenuItem Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Cms_Model_MenuItemMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Cms_Model_MenuItemMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Cms_Model_DbTable_MenuItem
     */
    protected $_dbTable;

    protected static $_translatedFields = array('name','uri','meta');

    protected static $_cacheTag = 'navigation';

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_MenuItem();
    }

    /**
     * get instance
     *
     *
     * @return Cms_Model_MenuItemMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function _rowToEntity(array $row, Cms_Model_MenuItem $item){
        $row = $this->_makeTranslationData($row, self::$_translatedFields);        
        $row['meta'] = $this->_getJsonData($row['meta']);
        $item->setOptions($row);
        $this->_parseParams($row['params'], $item);

    }

    private function _parseParams($paramsStr, Cms_Model_MenuItem $item, $old = false){        
        if($paramsStr == null || $paramsStr == ''){
            $item->set_params(array());
        }
        $params = array();
        $paramsArr = explode('/', $paramsStr);
        for($i = 0; $i < count($paramsArr) - 1; $i += 2){
            $params[$paramsArr[$i]] = $paramsArr[$i+1];
        }
        //append page id as param
        if(null !== $item->get_page_id()){
            $params['page_id'] = $item->get_page_id();
        }
        if($old){
            $item->set_params_old($params);  
        }else{
            $item->set_params($params);
        }
    }

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Cms_Model_MenuItem $item
     * @return boolean
     */
    public function find($id, Cms_Model_MenuItem $item,  $language = null) {
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('mi'=>'cms_menu_item'),array('mi.*'))
                ->where("mi.id = ?", $id);
        if(isset ($language)){
            $this->_makeTranslationJoin($language, $select, 'cms_menu_item', 'mi', 'id', self::$_translatedFields);
        }
        //echo $select->__toString();
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
     *
     * @return array
     */
    public function fetchAll($criteria = array(), $order = array()) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
            ->from(array('mi'=>'cms_menu_item'),array('mi.*'))
            ->joinLeft(array('m' => 'cms_menu'), "m.code = mi.menu",  array('menu_name' => 'name'));
        if(isset ($criteria['application_id'])){
            $select->where('mi.application_id = ?', $criteria['application_id']);
        }
        if(isset ($criteria['menu'])){
            $select->where('m.code = ?', $criteria['menu']);
        }
        if(isset ($criteria['lang'])){
            $this->_makeTranslationJoin($criteria['lang'], $select, 'cms_menu_item', 'mi', 'id', self::$_translatedFields);
        }
        if(isset ($criteria['visible_only'])){
            $select->where("mi.hidden = 'no'");
        }
        if(is_array($order) && count($order)){
            $select->order($order);
        }
//     echo $select->__toString();die;
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $items   = array();
        foreach ($resultSet as $row) {
            $item = new Cms_Model_MenuItem();
            $this->_rowToEntity($row->toArray(), $item);
            $items[] = $item;
        }
        return $items;
    }

    /**
     * Get page cache id
     *
     * @param array $options
     * @return string
     */
    protected function _getCacheId($criteria = array(), $order = array()){
        return "nav" . HCMS_Utils::base32Encode(serialize($criteria) . serialize($order));
    }

    /**
     * Clean all cached menus
     * 
     */
    public function cleanCache(){
        HCMS_Cache::getInstance()->getCoreCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array(self::$_cacheTag));
    }

    /**
     * Get Zend Navigation items
     * 
     * @param array $criteria
     * @return array
     */
    public function fetchZendNavigationArray($criteria = array(), $order = array(), $useCache = false){
        if($useCache){
            //$this->_initLogger();
            $cacheId = $this->_getCacheId($criteria, $order);
            $cache = HCMS_Cache::getInstance()->getCoreCache();
            if( ($result = $cache->load($cacheId)) !== FALSE ){
                //$this->_log("Zend navigation loaded from cache", Zend_Log::WARN);
                return $result;
            }
        }
        $items = $this->fetchAll($criteria, $order);
        if(!is_array($items)){
            return array();
        }
        /**
         * Tree structure is plain array where key is item ID from db table.
         * Every key has data like assocc array with all item data + array child_nodes where are ID's of all child nodes of item
         * There is only one string key called 'root', it points to ID of root of the tree, if it is 0 then there is no tree.
         * This representation of tree is good because it is filled with one pass by reading db and it can be processed recursively
         */
        $tree = array('root' => 0, 0 => array('child_nodes' => array()));
        /*@var $item Cms_Model_MenuItem */
        foreach ($items as $item) {
            // if there is no parent node of current node then it is out of order, we can only skip it
            if (!isset($tree[$item->get_parent_id()])) {
                continue;
            }
            // add current node in the list of parent's children
            $tree[$item->get_parent_id()]['child_nodes'][] = $item->get_id();
            // add current note in tree
            $tree[$item->get_id()]['entity'] = $item;
            // make empty children list for current node
            $tree[$item->get_id()]['child_nodes'] = array();
        }
        $links = $this->_generatePagesTree($tree, 0);
        //store cache
        if($useCache){
            //$this->_log("Zend navigation saved in cache", Zend_Log::WARN);
            $cache->save($links, $cacheId, array(self::$_cacheTag));
        }
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
            /*@var $entity Cms_Model_MenuItem */
            $entity = $tree[$id]['entity'];
            // extract module, controller and action from path
            $path = $entity->get_path();
            if(isset ($path)) {
                list($module,$controller,$action) = explode("/", $path);
                $pageType = 'mvc';
            }
            else {
                $pageType = 'uri';
            }
            $route = $entity->get_route();
            $target = $entity->get_target();
            if(isset ($route) && $route != ''){
                $item =  array(
                    'id'            => $entity->get_menu() . '-item-' . $entity->get_id(),
                    'data-mid'      => $entity->get_id(),
                    'label'         => $entity->get_name(),
                    'route'         => isset($route) && $route != ''?$route:null,
                    'module'        => isset($module)?$module:null,
                    'controller'    => isset($controller)?$controller:null,
                    'action'        => isset($action)?$action:null,
                    'params'        => $entity->get_params(),
                    'order'         => $entity->get_ord_num(),
                    'target'        => isset($target) && $target != ''?$target:null,
                    'entity'        => $entity,
                    'pages'         => array()
                );
            }
            else{
                $item =  array(
                    'id'            => $entity->get_menu() . '-item-' . $entity->get_id(),
                    'data-mid'      => $entity->get_id(),
                    'label'         => $entity->get_name(),
                    'order'         => $entity->get_ord_num(),
                    'uri'           => $entity->get_uri(),
                    'target'        => isset($target) && $target != ''?$target:null,
                    'entity'        => $entity,
                    'pages'         => array()
                );
            }

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
     * @param Cms_Model_MenuItem $item
     * @param string $language
     */
    public function save(Cms_Model_MenuItem $item, $language = null, $dontSaveRoute = false) {
        $data = array();
        
        $this->_entityToRow($item, $data);

        $id = $item->get_id();
       
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $item->set_id($id);
        } else {
            //if language is defined, save just untraslated strings
            if($language && $language != HCMS_Utils::getDefaultLocale()){
                foreach (self::$_translatedFields as $field) {
                    unset($data[$field]);
                }
            }
            $this->_dbTable->update($data, array('id = ?' => $id));
        }        
        //save in translation table
        if($language){
            $this->_saveTranslation('cms_menu_item', $item->get_id(), $item, self::$_translatedFields, $language);
            if($item->get_route() != '' && !$dontSaveRoute){
                $route = new Cms_Model_Route();
                $this->_parseParams((isset($data['params'])?$data['params']:""), $item);
                $this->_parseParams((($item->get_params_old()!='') ? $item->get_params_old():""), $item, true);
                $paramsOld = $item->get_params_old();
                if(isset($paramsOld['page_id']) && $paramsOld['page_id'] == ''){
                    unset($paramsOld['page_id']);
                }
                $item->set_params_old($paramsOld);
                if(!Cms_Model_RouteMapper::getInstance()->findByPath($item->get_path(), $item->get_application_id(), $route, $item->get_params_old(), $language)){
                    $route->set_lang($language)
                          ->set_application_id($item->get_application_id())
                          ->set_name($item->get_name())
                          ->set_path($item->get_path())
                          ->set_page_id($item->get_page_id_new())
                          ->set_uri($item->get_route_uri())
                          ->set_params($this->unsetParamsPageId($item->get_params()));
                    Cms_Model_RouteMapper::getInstance()->save($route);
                }else{
                    if($route->get_uri() != $item->get_route_uri() || $route->get_page_id() !=  $item->get_page_id_new() || 
                            $this->unsetParamsPageId($item->get_params()) != $this->unsetParamsPageId($item->get_params_old())){
                        $route->set_uri($item->get_route_uri());
                        $route->set_page_id($item->get_page_id_new());
                        $route->set_params($this->unsetParamsPageId($item->get_params()));
                        Cms_Model_RouteMapper::getInstance()->save($route);
                    }
                }
            }
        }
        $this->cleanCache();
    }
    
    
    public function unsetParamsPageId($params){
        if(isset($params['page_id'])){
            unset($params['page_id']);
        }
        $paramsString = '';
        $i=1;
        $countParams = count($params);
        if( $countParams> 0){
            foreach ($params as $key => $val) {
                
                $paramsString .= $key.'/'.$val;
                if($countParams > $i)
                    $paramsString .= '/';
                $i++;
            }
        }else{
            $paramsString = '';
        }
        return $paramsString;
    }


    protected function _entityToRow(Cms_Model_MenuItem $menu,array &$row){
        $null = new Zend_Db_Expr("NULL");
        $this->_populateDataArr($row, $menu, array('id','application_id','menu',
            'level','parent_id','page_id', 'page_id_new','name','route','path','params','uri', 'ord_num', 'hidden', 'meta', 'target'));
        (isset($row['meta']))?$row['meta'] = json_encode($row['meta']):'';
        (!isset($row['route']) || $row['route']=='')?$row['route'] = '':'';
        (!isset($row['page_id']) || $row['page_id']=='')?$row['page_id'] = $null:'';
        (!isset($row['page_id_new']) || $row['page_id_new']=='')?$row['page_id_new'] = $null:'';
        (!isset($row['path']) || $row['path']=='')?$row['path'] = $null:'';
        (!isset($row['uri']) || $row['uri']=='')?$row['uri'] = '':'';
        (!isset($row['target']) || $row['target']=='')?$row['target'] = $null:'';
        (!isset($row['params']) || $row['params']=='')?$row['params'] = $null:'';
        $row['page_id'] = $row['page_id_new'];
        unset($row['page_id_new']);
        if(!isset($row['parent_id'])){
            $row['parent_id'] = 0;
        }
        $row['level'] = $this->getLevel($row['parent_id']);
    }
    
    protected function getLevel($parentId = 0){
        if($parentId > 0){
            $select = $this->_dbTable->select();
            $select ->setIntegrityCheck(false)
                    ->from(array('mi'=>'cms_menu_item'),array('mi.level'))
                    ->where("mi.id = ?", $parentId);
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
    
    
    /**
     * delete menu item 
     *     
     * @param int $id
     */
    public function delete($id) {          
        $this->_dbTable->getAdapter()->query("DELETE FROM cms_menu_item_tr WHERE translation_id = ?", $id);        
        $this->_dbTable->delete(array('id = ?' => $id));        
    }
    
}