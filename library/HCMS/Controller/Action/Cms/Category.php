<?php
/**
 * Cms category action controller
 *
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */

class HCMS_Controller_Action_Cms_Category extends HCMS_Controller_Action_Cms {
    /**
     * Route name to use in url helpers for listing
     *
     * @var string
     */
    protected $_routeName = 'cms';

    protected $_listingParams = array(
        'module'        => 'cms',
        'controller'    => 'category',
        'action'        => 'index'
    );

    protected $_pageDispatchAuto = array(
        'enabled'       => false,
        'controller'    => 'page',
        'action'        => 'index'
    );

    /**
     * Pre-dispatch to page on url pattern category/page
     *
     * Called before action method. If using class with
     * {@link Zend_Controller_Front}, it may modify the
     * {@link $_request Request object} and reset its dispatched flag in order
     * to skip processing the current action.
     *
     * @return void
     */
    public function preDispatch(){
        if(!is_array($this->_pageDispatchAuto) || $this->_pageDispatchAuto['enabled'] !== true){
            return;
        }
        
        $urlId = $this->getRequest()->getParam("url_id");
        if($this->getRequest()->getActionName() != 'index' || !isset ($urlId) ) {
            return;
        }
        $urlParts = explode('/', $urlId);        
        if(count($urlParts) <= 1){
            return;
        }
        $this   ->getRequest()
                    ->setActionName($this->_pageDispatchAuto['action'])
                    ->setControllerName($this->_pageDispatchAuto['controller'])
                    ->setParam('url_id', $urlParts[count($urlParts) - 1])
                    ->setParam('category_url_id', $urlParts[count($urlParts) - 2])
                    ->setDispatched(false);
    }

    public function init(){
        if($this->getRequest()->getActionName() == 'index'){
            //for index action meta will go from category not menu
            $this->_isMetaFromActiveMenu = false;
        }
        parent::init();
    }

    /**
     * Single category page
     */
    public function indexAction() {
        $categoryId = $this->getRequest()->getParam("category_id");
        $urlId = $this->getRequest()->getParam("url_id");
        if(!isset ($categoryId) && !isset ($urlId) ) {
            throw new Zend_Controller_Action_Exception($this->translate("Category not found"),404);
        }
        $category = new Cms_Model_Category();
        //find by id
        if(isset ($categoryId)) {
            if(!Cms_Model_CategoryMapper::getInstance()->find($categoryId, $category, CURR_LANG)) {
                throw new Zend_Controller_Action_Exception(sprintf($this->translate("Category [%s] not found"),$categoryId),404);
            }
        }
        //find by url id
        else {
            if(!Cms_Model_CategoryMapper::getInstance()->findByUrlId($urlId, $category, CURR_LANG)) {
                throw new Zend_Controller_Action_Exception(sprintf($this->translate("Category [%s] not found"),$urlId),404);
            }
        }

        $this->view->headTitle($category->get_name(),Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
        $meta = $category->get_meta();
        foreach ($meta as $key => $value) {
            if($value != null && $value != ''){
                $this->view->headMeta()->appendName($key, $value);
            }
        }
        $this->view->category = $category;
        if(is_array($this->_pageDispatchAuto) && $this->_pageDispatchAuto['enabled'] === true){
            $this->view->urlIdPrefix = $category->get_url_id() . '/';
        }
        else{
            $this->view->urlIdPrefix = '';
        }
        $this->view->pageDispatchAuto = $this->_pageDispatchAuto;        
        //list pages from this category
        $this->_listPages($category->get_id());
        $this->view->listingParams = array(
            'module'        => 'cms',
            'controller'    => 'category',
            'action'        => 'index',
            'url_id'  => $category->get_url_id()
        );
        //call hook
        $this->_internalIndex($category);
    }

    protected function _listPages($categoryId, $typeId = null){
        $paging = array(
                'perPage'   => 1,
                'page'      => $this->getRequest()->getParam("page")
        );
        $criteria = array(
                'lang'          => CURR_LANG,
                'type_id'       => $typeId,
                'category_id'   => $categoryId,
                'status'        => 'published'
        );
        $orderBy = array('p.posted ASC');
        $pages = Cms_Model_PageMapper::getInstance()->fetchAll($criteria, $orderBy, $paging);
        $this->view->paginator = $paging['paginator'];
        $this->view->pages = $pages;
        $this->view->paging = $paging;
        $this->view->route = $this->_routeName;
    }

    /**
     * Index hook
     *
     * @param Cms_Model_Category $category
     */
    protected function _internalIndex($category) {

    }
    
    protected function _fetchCategories($criteria, $orderBy, &$paging){
        return Cms_Model_CategoryMapper::getInstance()->fetchAll($criteria, $orderBy, $paging);
    }

    /**
     * List of categories
     */
    public function listAction() {
         $paging = array(
                'perPage'   => 1,
                'page'      => $this->getRequest()->getParam("page")
        );
        $criteria = array(
                'lang'          => CURR_LANG,
                'type_id'       => isset ($this->_typeId)? $this->_typeId : $this->getRequest()->getParam("type_id")
        );
        $orderBy = array('c.name ASC');
        $this->_internalPreFetch($criteria, $orderBy, $paging);
        $categories = $this->_fetchCategories($criteria, $orderBy, $paging);
        $this->view->categories = $categories;
        $this->view->paginator = $paging['paginator'];
        $this->view->paging = $paging;
        $this->view->route = $this->_routeName;
        $this->view->listingParams = $this->_listingParams;
        //call hook
        $this->_internalList($categories);
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
     * @param array $categories
     */
    protected function _internalList($categories) {

    }
}