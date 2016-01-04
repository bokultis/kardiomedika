<?php
/**
 * Cms page action controller
 *
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */

class HCMS_Controller_Action_Cms_Page extends HCMS_Controller_Action_Cms {
    /**
     * Route name to use in url helpers for listing
     *
     * @var string
     */
    protected $_routeName = 'default';

    /**
     * Pages type id to be filtered by
     *
     * @var int
     */
    protected $_typeId = null;

    /**
     *
     * @var string
     */
    protected $_typeCode = null;

    const CMS_PAGE_CACHE_PRE = 'cms_page_view_';

    protected $_echoContent = true;

    protected $_listingParams = array(
        'module'        => 'cms',
        'controller'    => 'page',
        'action'        => 'list'
    );

    public function init(){
        if($this->getRequest()->getActionName() == 'index'){
            //for index action meta will go from page not menu
            $this->_isMetaFromActiveMenu = false;
        }
        parent::init();
    }

    protected function _fetchPage(Cms_Model_Page $page, $pageId, $urlId = null){
        if(isset ($pageId)) {
            if(!Cms_Model_PageMapper::getInstance()->find($pageId, $page, CURR_LANG)) {
                throw new Zend_Controller_Action_Exception(sprintf($this->translate("Page [%s] not found"),$pageId),404);
            }
        }
        //find by url id
        else {
            if(!Cms_Model_PageMapper::getInstance()->findByUrlId($urlId, $this->_applicationId, $page, CURR_LANG)) {
                throw new Zend_Controller_Action_Exception(sprintf($this->translate("Page [%s] not found"),$urlId),404);
            }
        }
        return true;
    }

    public function indexAction() {
        
        $pageId = $this->getRequest()->getParam("page_id");
        $urlId = $this->getRequest()->getParam("url_id");
        if(!isset ($pageId) && !isset ($urlId) ) {
            throw new Zend_Controller_Action_Exception($this->translate("Page not found"),404);
        }
        $page = new Cms_Model_Page();
        //fetch page
        $this->_fetchPage($page, $pageId, $urlId);
        //check if page is published
        if('published' != $page->get_status()) {
            throw new Zend_Controller_Action_Exception(sprintf($this->translate("You are not allowed to enter this page."),$pageId),403);
        }

        $this->view->headTitle($page->get_title(),Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
        $meta = $page->get_meta();
        foreach ($meta as $key => $value) {
            if($value != null && $value != ''){
                $this->view->headMeta()->appendName($key, $value);
            }
        }
        
        //fb og
        $this->view->headMeta()->setProperty('og:type', 'article');
        if($page->get_title() != '') $this->view->headMeta()->setProperty('og:title', $page->get_title() . ' - ' . $this->_application->get_name());
        if($page->get_teaser() != '') $this->view->headMeta()->setProperty('og:description', $page->get_teaser());
        
        //call hook
        $this->_internalIndex($page);
        $this->view->page = $page;
        
        //custom view helper for rendering pages
        $pageTypes = $this->_application->get_settings("page_types");
        if(isset($pageTypes)){
            //render with custom view helper
            if(isset($pageTypes[$page->get_type_id()]['view_helper'])){
                $this->_helper->viewRenderer->setNoRender(true);
                $viewHelper = $pageTypes[$page->get_type_id()]['view_helper'];            
                echo $this->view->$viewHelper($page);
                return;
            //render with custom template
            } elseif(isset($pageTypes[$page->get_type_id()]['template'])){
                $this->_helper->viewRenderer->setNoRender(true);
                $this->renderScript($pageTypes[$page->get_type_id()]['template']);
                return;                  
            }

        }        

        $pageFormat = $page->get_format();
        //render differently based on format
        switch($pageFormat) {
            case 'html':
                if($this->_echoContent){
                    $this->_helper->viewRenderer->setNoRender(true);
                    echo $page->get_content();
                }
                break;
            case 'php':
                $this->_renderPageAsView($page);
                break;
            case 'path':
                $this->renderScript('page/static/' .$page->get_content());
                break;
        }
    }

    /**
     * Stores content as view script in cache and renders it
     * 
     * @param Cms_Model_Page $page
     */
    protected function _renderPageAsView(Cms_Model_Page $page) {                
        $cacheId = self::CMS_PAGE_CACHE_PRE . $page->get_id();
        $resources = $this->getInvokeArg('bootstrap')->getOption('resources');
        $cacheDir = $resources['cachemanager']['core']['backend']['options']['cache_dir'];
        $cacheFileName = 'zend_cache---' . $cacheId;
        // Create Zend_Cache_Core object
        $cache =  HCMS_Cache::getInstance()->getCoreCache();
        // If no cache available for this content page
        if(!$cache->test($cacheId)){
            $cache->save((string)$page->get_content(), $cacheId);
            //forcing raw string :(
            file_put_contents($cacheDir . "/" . $cacheFileName, $page->get_content());
        }        
        $this->view->addScriptPath($cacheDir);
        $this->renderScript($cacheFileName);
    }

    /**
     * Index hook
     *
     * @param Cms_Model_Page $page
     */
    protected function _internalIndex($page) {

    }

    protected function _fetchPages($criteria, $orderBy, &$paging){
        return Cms_Model_PageMapper::getInstance()->fetchAll($criteria, $orderBy, $paging);
    }

    public function listAction() {
        $paging = array(
                'perPage'   => 1,
                'page'      => $this->getRequest()->getParam("page")
        );
        $criteria = array(
                'lang'          => CURR_LANG,
                'user_id'       => $this->getRequest()->getParam("user_id"),
                'category_id'   => $this->getRequest()->getParam("category_id"),
                'status'        => 'published'
        );
        if(isset ($this->_typeId)){
            $criteria['type_id'] = $this->_typeId;
        }
        elseif(isset ($this->_typeCode)){
            $criteria['type_code'] = $this->_typeCode;
        }
        elseif(null != $this->getRequest()->getParam("type_id")){
            $criteria['type_id'] = $this->getRequest()->getParam("type_id");
        }
        elseif(null != $this->getRequest()->getParam("type_code")){
            $criteria['type_code'] = $this->getRequest()->getParam("type_code");
        }

        $orderBy = array('p.posted ASC');
        $this->_internalPreFetch($criteria, $orderBy, $paging);
        $pages = $this->_fetchPages($criteria, $orderBy, $paging);
        $this->view->paginator = $paging['paginator'];
        $this->view->pages = $pages;
        $this->view->paging = $paging;
        $this->view->route = $this->_routeName;
        $this->view->listingParams = $this->_listingParams;
        //call hook
        $this->_internalList($pages);
    }

    /**
     * Chance to alter fetch parameters
     *
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     */
    protected function _internalPreFetch(&$criteria, &$orderBy, &$paging) {

    }

    /**
     * List hook
     *
     * @param array $pages
     */
    protected function _internalList($pages) {

    }

    /**
     * RSS feed action
     */
    public function feedAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        /**
         * Create the parent feed
         */
        $feed = new Zend_Feed_Writer_Feed();

        $feed->setTitle($this->_application->get_name());
        $feed->setLink($this->view->serverUrl());

        $feed->setDescription($this->_application->get_name());
        $feed->setFeedLink($this->view->serverUrl() . $this->view->url(array(
            'module'        => $this->getRequest()->getModuleName(),
            'controller'    => $this->getRequest()->getControllerName(),
            'action'        => $this->getRequest()->getActionName()
        ),'cms'),'rss');

        $feed->setDateModified(time());

        $criteria = array(
            'lang'          => CURR_LANG,
            'user_id'       => $this->getRequest()->getParam("user_id"),
            'category_id'   => $this->getRequest()->getParam("category_id"),
            'status'        => 'published'
        );
        if(isset ($this->_typeId)){
            $criteria['type_id'] = $this->_typeId;
        }
        elseif(isset ($this->_typeCode)){
            $criteria['type_code'] = $this->_typeCode;
        }
        elseif(null != $this->getRequest()->getParam("type_id")){
            $criteria['type_id'] = $this->getRequest()->getParam("type_id");
        }
        elseif(null != $this->getRequest()->getParam("type_code")){
            $criteria['type_code'] = $this->getRequest()->getParam("type_code");
        }
        
        $this->_addEntries($feed, $criteria);
        
        $this->getResponse()->setHeader('Content-type', 'application/rss+xml; charset=UTF-8');
        echo $feed->export('rss');
    }

    /**
     * Add entries
     * 
     * @param array $criteria
     * @return array
     */
    protected function _addEntries(Zend_Feed_Writer_Feed $feed,$criteria){
        //always show 50 newest
        $paging = array(
                'perPage'   => 50,
                'page'      => 1
        );
        $orderBy = array('p.posted DESC');
        $pages = Cms_Model_PageMapper::getInstance()->fetchAll($criteria, $orderBy, $paging);
        /*@var $page Cms_Model_Page */
        foreach ($pages as $page) {
            if(null == $page->get_content() || '' == $page->get_content()){
                continue;
            }
            $entry = $feed->createEntry();
            $this->_populateFeedEntry($page, $entry);
            $feed->addEntry($entry);
        }
    }

    /**
     * Populate feed entry data
     * 
     * @param Cms_Model_Page $page
     * @param Zend_Feed_Entry_Abstract $entry
     */
    protected function _populateFeedEntry(Cms_Model_Page $page, Zend_Feed_Writer_Entry $entry){
        $entry->setTitle($page->get_title());
        $entry->setLink($this->view->serverUrl() . $this->view->url(array(
            'module'        => $this->getRequest()->getModuleName(),
            'controller'    => $this->getRequest()->getControllerName(),
            'action'        => 'index',
            'url_id'        => $page->get_url_id()
        ),'cms'));
        if(null != $page->get_user_name() && '' != $page->get_user_name()){
            $entry->addAuthor(array('name'  => $page->get_user_name()));
        }
        $entry->setDateModified(new Zend_Date($page->get_posted(), Zend_Date::ISO_8601));
        if(null != $page->get_teaser() && '' != $page->get_teaser()){
            $entry->setDescription($page->get_teaser());
        }
        $entry->setContent($this->_fixRelImagePaths($page->get_content()));
    }

    /**
     * Convert img rel paths to abs for feed
     * 
     * @param string $html
     * @return string
     */
    protected function _fixRelImagePaths($html){
        $DOM = new DOMDocument;
        $DOM->loadHTML($html);

        $imgs = $DOM->getElementsByTagName('img');
        if(!isset ($imgs) || $imgs->length == 0){
            return $html;
        }
        $webRoot = $this->view->serverUrl() . $this->view->fileWebRoot;
        foreach($imgs as $img){
            $src = $img->getAttribute('src');
            if(strpos($src, $webRoot) !== 0){
                $img->setAttribute('src', $webRoot . $src);
            }
        }
        $dom->formatOutput = false;
        
        return $DOM->saveHTML();
    }
}