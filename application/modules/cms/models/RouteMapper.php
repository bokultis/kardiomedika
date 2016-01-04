<?php
/**
 * Route Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_RouteMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Cms_Model_RouteMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Cms_Model_DbTable_CmsRoute
     */
    protected $_dbTable;

    /**
     * Routes are loaded from db or not
     * @var boolean
     */
    protected $_routesLoaded = false;

    /**
     * Routes index by id
     * 
     * @var array
     */
    protected $_routes = array();

    /**
     * Routes index by uri
     * 
     * @var array
     */
    protected $_routesUri = array();

    /**
     * Routes indexed by path (path+params)
     * @var array
     */
    protected $_routesPath = array();

    /**
     * Wildcard routes indexed by uri
     * 
     * @var array
     */
    protected $_routesWildcard = array();

    protected static $_cacheId = 'routes';

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Cms_Model_DbTable_Route();
        $this->_initLogger();
    }

    /**
     * get instance
     *
     *
     * @return Cms_Model_RouteMapper
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
     * Parse params string to array
     * 
     * @param string $paramsStr
     * @param Cms_Model_Route $route
     */
    private function _parseParams($paramsStr, Cms_Model_Route $route){
        if($paramsStr == null || $paramsStr == ''){
            $route->set_params(array());
        }
        $params = array();
        $paramsArr = explode('/', $paramsStr);
        for($i = 0; $i < count($paramsArr) - 1; $i+=2){
            $params[$paramsArr[$i]] = $paramsArr[$i+1];
        }
        //append page id as param
        if(null !== $route->get_page_id()){
            $params['page_id'] = $route->get_page_id();
        }
        $route->set_params($params);
    }

    /**
     * Load routes from cache
     * 
     * @return boolean
     */
    private function _loadRoutesFromCache(){
        $cache = HCMS_Cache::getInstance()->getCoreCache();
        if( ($result = $cache->load(self::$_cacheId)) === FALSE ){
            return FALSE;
        }
        $this->_routes = $result['all'];
        $this->_routesUri = $result['uri'];
        $this->_routesPath = $result['path'];
        $this->_routesWildcard = $result['wildcard'];
        $this->_routesLoaded = true;

        //$this->_initLogger();
        //$this->_log("Routes loaded from cache", Zend_Log::INFO);
        return true;
    }

    /**
     * Save routes to cache
     * 
     * @return boolean
     */
    private function _saveRoutesToCache(){
        $result = array(
            'all'   => $this->_routes,
            'uri'   => $this->_routesUri,
            'path'   => $this->_routesPath,
            'wildcard'   => $this->_routesWildcard
        );
        HCMS_Cache::getInstance()->getCoreCache()->save($result, self::$_cacheId);
        //$this->_initLogger();
        //$this->_log("Routes saved to cache", Zend_Log::INFO);
        return true;
    }

    /**
     * Invalidate routes cache
     * 
     */
    public function cleanCache(){
        HCMS_Cache::getInstance()->getCoreCache()->remove(self::$_cacheId);
    }

    /**
     * Load routes from DB into memory arrays
     * 
     * @return mixed
     */
    private function _loadRoutes(){
        //try to load routes from cache
        if($this->_loadRoutesFromCache()){
            return true;
        }
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('r'=>'cms_route'),array('r.*'));
        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {            
            return false;
        }
        foreach ($resultSet as $row) {
            $route = new Cms_Model_Route($row->toArray());            
            $this->_parseParams($row->params, $route);
            $this->_routes[$route->get_id()] = $route;
            $this->_routesLoaded = true;
            $lang = ($route->get_lang() !== null)?$route->get_lang():'all';
            //map by uri
            if(!isset($this->_routesUri[$lang])){
                $this->_routesUri[$lang] = array();
            }
            $this->_routesUri[$lang][$route->get_uri()] = $route->get_id();
            //map by path
            if(!isset ($this->_routesPath[$lang])){
                $this->_routesPath[$lang] = array();
            }
            //map by wildcard
            if(substr($route->get_uri(), -1) == '%'){
                if(!isset ($this->_routesWildcard[$lang])){
                    $this->_routesWildcard[$lang] = array();
                }
                $this->_routesWildcard[$lang][substr($route->get_uri(), 0, -1)] = $route->get_id();
                //special path for wildcard
                $path = '%/' . $route->get_path() . "/" . $this->generateParamsString($route->get_params());
                $this->_routesPath[$lang][$path] = $route->get_id();
            }
            else{
                //map by path
                $path = $route->get_path() . "/" . $this->generateParamsString($route->get_params());
                $this->_routesPath[$lang][$path] = $route->get_id();
            }
        }
        //save routes to cache
        $this->_saveRoutesToCache();
        //$this->_log(sprintf("loaded routes: %s, uris: %s, paths: %s",
        //        json_encode($this->_routes), json_encode($this->_routesUri), json_encode($this->_routesPath)) , Zend_Log::DEBUG);
    }

    /**
     * Find a route from wildcard routes
     * 
     * @param string $match
     * @param Cms_Model_Route $route
     * @param string $lang
     * @return boolean
     */
    function _findFromWildCards($match, Cms_Model_Route $route, $lang = null){
        if(!isset ($lang)){
            $lang = 'all';
        }

        if(!isset ($this->_routesWildcard[$lang])){
            return false;
        }

        foreach ($this->_routesWildcard[$lang] as $pattern => $routeId) {
            if(substr($match, 0, strlen($pattern)) == $pattern){
                $foundRoute = $this->_routes[$routeId];
                $route->setOptions($foundRoute->toArray());
                $params = $route->get_params();
                //set url_id param
                $params['url_id'] = substr($match, strlen($pattern));
                $route->set_params($params);
                return true;
            }
        }
        return false;
    }

    /**
     * Find route from memory array
     * 
     * @param string $match
     * @param array $arr
     * @param Cms_Model_Route $route
     * @param string $lang
     * @return boolean
     */
    protected function _findFromMemory($match, array $arr, Cms_Model_Route $route, $lang = null){        
        if(!isset ($lang)){
            $routeId = isset ($arr['all'][$match])? $arr['all'][$match]:null;            
        }
        else{
            $routeId = isset ($arr[$lang][$match])? $arr[$lang][$match]:null;            
            //try from all
            if(!isset ($routeId)){
                $routeId = isset ($arr['all'][$match])? $arr['all'][$match]:null;
            }
        }        
        if(!isset ($routeId) || !isset ($this->_routes[$routeId])){
            return false;
        }
        $foundRoute = $this->_routes[$routeId];        
        $route->setOptions($foundRoute->toArray());        
        return true;
        
    }

    /**
     * Find by uri
     *
     * @param string $uri
     * @param int $applicationId
     * @param Cms_Model_Route $route
     * @return boolean
     */
    public function findByUri($uri, $applicationId, Cms_Model_Route $route, $lang = null) {
        //read from memory
        if(!$this->_routesLoaded){
            $this->_loadRoutes();
        } 
        //read from memory
        $result = $this->_findFromMemory($uri, $this->_routesUri, $route, $lang);
        if($result === false){
            //try wildcards
            return $this->_findFromWildCards($uri, $route, $lang);
        }
        else{
            return true;
        }
    }

    /**
     * Find by module/controller/action/
     *
     * @param string $path
     * @param int $applicationId
     * @param Cms_Model_Route $route
     * @param array $params
     * @return boolean
     */
    public function findByPath($path, $applicationId, Cms_Model_Route $route, $params = array(), $lang = null) {
        //read from memory
        if(!$this->_routesLoaded){
            $this->_loadRoutes();
        } 
        //read from memory        
        $result =  $this->_findFromMemory($path . "/" . $this->generateParamsString($params), $this->_routesPath, $route, $lang);
        if($result == true){            
            return true;
        }        
        //find by wildcard
        if(!isset ($params['url_id'])){
            return false;
        }
        $urlId = $params['url_id'];
        unset($params['url_id']);
        //$this->_log(' Searching wildcard path : [%/' . $path . "/" . $this->generateParamsString($params) . '] in :' . json_encode($this->_routesPath), Zend_Log::DEBUG);
        $result =  $this->_findFromMemory('%/' . $path . "/" . $this->generateParamsString($params), $this->_routesPath, $route, $lang);
        if($result == true){
            //replace wildcard with urlId
            $uri = $route->get_uri();
            $route->set_uri(str_replace('%', $urlId, $uri));
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Find by pageId
     *
     * @param int $pageId
     * @param Cms_Model_Route $route
     * @param string $lang
     * @return boolean
     */
    public function findByPageId($pageId, Cms_Model_Route $route, $lang = null) {
        //read from memory
        if(!$this->_routesLoaded){
            $this->_loadRoutes();
        }
        if(!isset ($lang)){
            $lang = 'all';
        }

        if(!isset ($this->_routesPath[$lang])){
            return false;
        }

        foreach ($this->_routesPath[$lang] as $path => $routeId) {
            $foundRoute = $this->_routes[$routeId];
            if($foundRoute->get_page_id() != $pageId){
                continue;
            }
            $route->setOptions($foundRoute->toArray());
            return true;
        }

        return false;
    }

    /**
     * Get params string from array
     * 
     * @param array $params
     * @return string
     */
    public function generateParamsString($params){
        $paramsStr = '';
        if(is_array($params) && count($params) > 0){
            foreach ($params as $paramName => $paramValue) {
                if(!is_scalar($paramValue)){
                    continue;
                }
                $currParam = "$paramName/$paramValue";
                if($paramsStr == ""){
                    $paramsStr = $currParam;
                }
                else{
                    $paramsStr .= "/" . $currParam;
                }

            }
        }
        return $paramsStr;
    }
    
     /**
     * Save entity
     *
     * @param Cms_Model_Route $route
     */
    public function save(Cms_Model_Route $route) {
        $data = array();

        $this->_entityToRow($route, $data);

        $id = $route->get_id();
        
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $route->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }

        $this->cleanCache();
    }
    
    protected function _entityToRow(Cms_Model_Route $route,array &$row){
        $this->_populateDataArr($row, $route, array('id','application_id','uri',
            'name','lang','path','params','page_id'));
    }
    
    /**
    *  Find all routes
    * 
    * @param array $criteria
    * @param array $order
    * @param array $paging
    * @return \Cms_Model_Route 
    */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
            ->from(array('r'=>'cms_route'),array('r.*'))
            ->joinLeft(array('p' => 'cms_page'), "p.id = r.page_id",  array('page_title' => 'title'));
    
        if(array_key_exists('lang', $criteria)){
            if(isset ($criteria['lang']) && $criteria['lang'] != ''){
                $select->where('r.lang = ?', $criteria['lang']);
            }
            else{
                $select->where('r.lang IS NULL');
            }
        }
        if(isset ($criteria['path'])){
            $select->where('r.path = ?', $criteria['path']);
        }
        
        if(isset ($criteria['uri'])){
            $select->where('r.uri = ?', $criteria['uri']);
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
        $routes   = array();
        foreach ($resultSet as $row) {
            $route = new Cms_Model_Route($row->toArray());
            $routes[] = $route;
        }
        return $routes;
    }
    
    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Cms_Model_Route $item
     * @return boolean
     */
    public function find($id, Cms_Model_Route $route) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                ->from(array('r'=>'cms_route'),array('r.*'))
                ->joinLeft(array('p' => 'cms_page'), "p.id = r.page_id",  array('page_title' => 'title', 'page_id' => 'id'))
                ->where("r.id = ?", $id);
       
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $rowArray = $row->toArray();
        $route->setOptions($rowArray);
        return true;
    }
    
    public function checkRouteExist(Cms_Model_Route $existingRoute, $applicationId){
        $params = array();
        $params = $this->_parseParams($existingRoute->get_params(), $existingRoute);
        if($existingRoute->get_page_id() != '')
            $params['page_id'] = $existingRoute->get_page_id();
        if($this->findByUri($existingRoute->get_uri(), $applicationId, $existingRoute, $existingRoute->get_lang())){
            return true;
        }
        if($this->findByPath($existingRoute->get_path(), $applicationId, $existingRoute, $params, $existingRoute->get_lang())){
            return true;
        }
        return false;
    }
    /**
     * Delete Route
     * 
     * @param int $ids 
     */
    public function delete(Cms_Model_Route $route){
        $result = $this->_dbTable->getAdapter()->delete('cms_route', array(
            'id = ?'   => $route->get_id()
        ));
        
        return ($result > 0);
    }
}