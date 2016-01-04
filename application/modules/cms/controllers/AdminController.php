<?php

/**
 * Admin controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_AdminController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;

    /**
     * @var Cms_Model_PageType
     */
    protected $_pageType = null;
    protected $_defaultLang = null;

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
        $this->_defaultLang = Application_Model_TranslateMapper::getInstance()->getDefaultLang();
    }

    /**
     * List pages
     */
    public function pageAction() {
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->types = Cms_Model_PageTypeMapper::getInstance()->fetchAll();
        $this->view->picker = HCMS_Utils_Date::resolveZendLocaleToDatePickerFormat();
        $this->view->defaultLang = $this->_defaultLang;
        $categories = Cms_Model_CategoryMapper::getInstance()->fetchZendNavigationArray(array('type_id' => 2));
//        print_r($categories);die;
        $this->view->categories = $categories;
        //filter by type code
        $typeCode = $this->_getParam('type_code');
        if(isset ($typeCode)){
            $this->_pageType = new Cms_Model_PageType();
            if(Cms_Model_PageTypeMapper::getInstance()->findByCode($typeCode, $this->_pageType)){
                $this->view->pageType = $this->_pageType;
            }
        }
    }

    /**
     * Ajax listing of pages
     */
    public function pageListAction() {
        //criteria
        $criteria = array(
            'application_id' => $this->_applicationId
        );
        if (null != $this->_getParam('langFilter')) {
            $criteria['lang'] = $this->_getParam('langFilter');
        }
        if (null != $this->_getParam('typeFilter')) {
            $criteria['type_id'] = $this->_getParam('typeFilter');
        }
        if (null != $this->_getParam('categoryFilter')) {
            $criteria['category_id'] = $this->_getParam('categoryFilter');
        }
        if(null != $this->_getParam('statusFilter')){
            $criteria['status'] = $this->_getParam('statusFilter');
        }
        if(null != $this->_getParam('menuFilter')){
            $criteria['menu_item_id'] = $this->_getParam('menuFilter');
        }        
        if(null != $this->_getParam('searchFilter')){
            $criteria['search_filter'] = $this->_getParam('searchFilter');
        }
        //order
        $order = $this->_request->getParam('order');
        if (isset($order)) {
            $order = array($order);
        } else {
            $order = array("p.title ASC");
        }
        //paging
        $page = $this->_request->getParam('page');
        $perPage = $this->_request->getParam('perPage');
        if(!isset ($page) || $page < 1){
            $page = 1;
        }
        if(!isset ($perPage) || $perPage < 1 || $perPage > 300){
            $perPage = 20;
        }
        $paging = array(
            'page'      => $page,
            'perPage'   => $perPage
        );
        $records = Cms_Model_PageMapper::getInstance()->fetchAll($criteria, $order, $paging);
        $data = array(
            'total' => $paging['total'],
            'page' => $paging['page'],
            'records' => $paging['records'],
            'perPage' => $paging['perPage'],
            'rows' => array()
        );

        /* @var $record Cms_Model_Page */
        foreach ($records as $record) {
            //find route to page
            $cmsRoute = new Cms_Model_Route();
            $lang = (null != $this->_getParam('langFilter'))? $this->_getParam('langFilter'): CURR_LANG;
            if(!Cms_Model_RouteMapper::getInstance()->findByPageId($record->get_id(), $cmsRoute, $lang)){
                //default path
                $module = 'cms';
                $controller = 'page';
                $action = 'index';
            }
            else{
                list($module,$controller,$action) = explode("/", $cmsRoute->get_path());
            }
            $urlParams = array(
                'module'        => $module,
                'controller'    => $controller,
                'action'        => $action,
                'page_id'       => $record->get_id(),
                'lang'          => $lang
            );
            $data['rows'][] = array(
                'id'        => $record->get_id(),
                'title'     => $record->get_title(),
                'code'      => $record->get_code(),
                'url_id'    => $record->get_url_id(),
                'type_id'   => $record->get_type_id(),
                'type_name' => $this->translate($record->get_type_name()),
                'type_code' => $record->get_type_name(),
                'user_name' => $record->get_user_name(),
                'status'    => $this->translate($record->get_status()),
                'posted'    => HCMS_Utils_Time::timeMysql2Local($record->get_posted()),
                'url'       => $this->view->url($urlParams,'cms',true)
            );
        }

        $this->_helper->json->sendJson($data);
    }

    public function pageEditAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $typeId = $this->_getParam('type_id');
        $langFilter = $this->_getParam('langFilter');

        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'page')), $this->translate('Action canceled'));
        }        

        //create form object
        $form = new Cms_Form_Page($data,null,$langFilter);

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();                
                //create entity object from submitted values, and save
                $page = new Cms_Model_Page($values);
                //new entity
                if(!isset ($data['id']) || $data['id'] <= 0){
                    $page   ->set_application_id($this->_applicationId)
                            ->set_user_id($this->_admin->get_id())
                            ->set_posted(HCMS_Utils_Time::timeTs2Mysql(time()));
                }
                else{                    
                    $existingPage = new Cms_Model_Page();
                    if(!Cms_Model_PageMapper::getInstance()->find($data['id'], $existingPage)){
                        throw new Exception("Page not found");
                    }
                    if((int)$existingPage->get_application_id() != $this->_applicationId){
                        throw new Exception("Cannot edit this Page.");
                    }                    
                }                
                Cms_Model_PageMapper::getInstance()->save($page,$langFilter);
                //save categories
                if(isset ($data['categories'])){
                    Cms_Model_PageMapper::getInstance()->saveCategories($page, $data['categories']);
                }
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'page')), $this->translate('Page saved.'));
            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        //first run of the form - grab data from mapper
        elseif(!$this->_formHelper->getRequest()->isPost()) {
            //edit action
            if(isset ($id) && $id > 0) {
                $page = new Cms_Model_Page();
                if(!Cms_Model_PageMapper::getInstance()->find($id, $page, $langFilter)){
                    throw new Exception("Page not found");
                }
                //fetch data
                $data = $page->toArray();
                //populate form with data
                $form->setData($data);
            }
            else{
                $this->view->typeId = $typeId;
            }
        }

        $this->view->data = $data;
        $this->view->types = Cms_Model_PageTypeMapper::getInstance()->fetchAll();
        //custom elements
        $pageTypes = $this->_application->get_settings("page_types");
        $typeId  = ($this->_getParam('type_id'))? $this->_getParam('type_id'): $page->get_type_id();
        $this->view->customElements = (isset($pageTypes) && isset($pageTypes[$typeId]['custom_elements']))? $pageTypes[$typeId]['custom_elements']: array();
    }

    public function categoriesAction(){
        if($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
        }
        $typeId = $this->getRequest()->getParam('type_id');
        $pageId = $this->getRequest()->getParam('page_id');

        //get all categories
        $categories = Cms_Model_CategoryMapper::getInstance()->fetchZendNavigationArray(array('type_id' => $typeId), array('parent_id ASC'));

        //get defined categories
        $checked = array();
        if(isset ($pageId) && $pageId > 0){            
            $page = new Cms_Model_Page();
            if(Cms_Model_PageMapper::getInstance()->find($pageId, $page)){
                $checked = Cms_Model_PageMapper::getInstance()->fetchCategories($page);
            }

        }        
        $this->_helper->json(array(
            'categories'            => $categories,
            'checked'               => $checked
        ));
    }

    public function widgetAction(){
        if($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
        }        
    }

    public function urlIdAction(){
        $data = array(
            'url_id'    =>  $this->_getParam('title'),
            //'lang'      =>  $this->_getParam('lang')
        );

        $form = new Cms_Form_Page($data);
        $result = $form->getEscaped('url_id');
        $this->_helper->json(array(
            'url_id' => $result
        ));
    }
    
    public function dialogAction(){
        $page_id = $this->getRequest()->getParam('page_id', '');
        $langFilter = $this->getRequest()->getParam('langFilter', '');
        $data = $this->getRequest()->getPost('data');
        
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->types = Cms_Model_PageTypeMapper::getInstance()->fetchAll();
        $this->view->page_id = $page_id;
        $this->view->langFilter = $langFilter;
        if ($this->_formHelper->isSave()) {         
            return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'menu')), $this->translate('Page Selected.'), $data);
        }
    }

    public function pageDeleteAction(){
        $id = $this->_getParam('page_id');

        $page = new Cms_Model_Page();
        if(!Cms_Model_PageMapper::getInstance()->find($id, $page)){
            return $this->_formHelper->returnError($this->translate('Page not found.'));
        }
        if((int)$page->get_application_id() != $this->_applicationId){
            return $this->_formHelper->returnError($this->translate('Cannot delete this Page.'));
        }
        //check if page is in use
        if(Cms_Model_PageMapper::getInstance()->isInUse($page)){
            return $this->_formHelper->returnError($this->translate('This page is in use in menus or routes.'));
        }
        Cms_Model_PageMapper::getInstance()->delete($page);
        //save done, return success
        return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'page')), $this->translate('Page deleted.'));
    }

}