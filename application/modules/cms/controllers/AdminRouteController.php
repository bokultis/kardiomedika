<?php

/**
 * Admin Route controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Cms_AdminRouteController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;
    protected $_modules = null;
    protected $_languages = null;
    
    protected $_defaultLang = null;

    protected $_authPrivilege = 'master';
    
    
    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
        $criteria['application_id'] = $this->_applicationId;
        $this->_modules =  Application_Model_ModuleMapper::getInstance()->fetchAll($criteria);
        $this->_languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->_defaultLang = Application_Model_TranslateMapper::getInstance()->getDefaultLang();
        
    }

    /**
     * List route
     */
    public function indexAction() {
        $this->view->languages = $this->_languages;
        /* get all modules */
        $this->view->modules = $this->_modules;
        
        $this->view->defaultLang = $this->_defaultLang;
    }

    /**
     * Ajax listing of routes
     */
    public function listAction() {
        //criteria
        $criteria = array(
            'application_id' => $this->_applicationId
        );
        
        if(null != $this->_getParam('searchFilter')){
            $criteria['search_filter'] = $this->_getParam('searchFilter');
        }
        if(null != $this->_getParam('langFilter')){
            $criteria['lang'] = $this->_getParam('langFilter');
        }
        if(null != $this->_getParam('pathFilter')){
            $criteria['path'] = $this->_getParam('pathFilter');
        }
        
        //order
        $order = $this->_request->getParam('order');
        if (isset($order)) {
            $order = array($order);
        } else {
            $order = array("r.name ASC");
        }
        
        //paging
        $page = $this->_request->getParam('page');
        $perPage = $this->_request->getParam('perPage');
        if(!isset ($page) || $page < 1){
            $page = 1;
        }
        if(!isset ($perPage) || $perPage < 1 || $perPage > 300){
            $perPage = 10;
        }
        $paging = array(
            'page'      => $page,
            'perPage'   => $perPage
        );
        $records = Cms_Model_RouteMapper::getInstance()->fetchAll($criteria, $order, $paging);

        $data = array(
            'total' => $paging['total'],
            'page' => $paging['page'],
            'records' => $paging['records'],
            'perPage' => $paging['perPage'],
            'rows' => array()
        );
//        print_r($this->_filePaths);die;
        /* @var $record Cms_Model_Page */
        foreach ($records as $record) {
            $data['rows'][] = array(
                'id' => $record->get_id(),
                'uri' => $record->get_uri(),
                'name' => $record->get_name(),
                'lang' => $record->get_lang(),
                'path' => $this->getDestinationModule($record->get_path()),//$record->get_path(), 
                'params' => $record->get_params(),
                'page_title' => $record->get_page_title()
            );
        }
        
        $this->_helper->json->sendJson($data);
    }
    
    private function getDestinationModule($routePath){
        foreach($this->_modules as $module) {
            $data = $module->get_data();
            if(isset($data['menus'])){
                foreach($data['menus'] as $path => $menu) {
                     if($module->get_code()."/".$path == $routePath) {
                         return '<strong>'. $module->get_name().'</strong>::'. $menu['name'];
                     }
                }
            }
        }
    }

    public function editAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $data['lang'] = $this->_getParam('langFilter');        
        
        /* get all modules */
        $criteria['application_id'] = $this->_applicationId;
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll($criteria);
       
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }
        if(isset($data['path']) && $data['path'] != ''){
            foreach ($modules as $module){
                $moduleData = $module->get_data();
                if(isset($moduleData['menus'])){
                    foreach($moduleData['menus'] as $path => $menu) {
                        if($module->get_code()."/".$path == $data['path']){
                            $data['dialog_url'] = $menu['dialog_url'];
                        }
                    }
                }
            }
        }
//        print_r($data);die;
        //create form object
        $form = new Cms_Form_Route($data);
        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();
                //create entity object from submitted values, and save
                $route = new Cms_Model_Route($values);                
                
                //new entity
                if(!isset ($data['id']) || $data['id'] <= 0){
                    $route->set_application_id($this->_applicationId);
                    $existingRoute = clone $route;
                    if(Cms_Model_RouteMapper::getInstance()->checkRouteExist($existingRoute, $this->_applicationId))
                       $route->set_id($existingRoute->get_id());
                }
                else{                    
                    $existingRoute = new Cms_Model_Route();
                    if(!Cms_Model_RouteMapper::getInstance()->find($data['id'], $existingRoute)){
                        throw new Exception("Menu not found");
                    }
                    if((int)$existingRoute->get_application_id() != $this->_applicationId){
                        throw new Exception("Cannot edit this Route.");
                    }                    
                } 
                $route->set_application_id($this->_applicationId);
                Cms_Model_RouteMapper::getInstance()->save($route);                
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'index')), $this->translate('Route saved.'));
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
                $route = new Cms_Model_Route();
                if(!Cms_Model_RouteMapper::getInstance()->find($id, $route)){
                    throw new Exception("Route not found");
                }
                $data = $route->toArray();
                //populate form with data
                $form->setData($data);
            }
        }
        
        $this->view->languages =  Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->modules = $modules;
        $page = new Cms_Model_Page();
        if(isset($data['page_id']))
            Cms_Model_PageMapper::getInstance()->find($data['page_id'], $page);
        $this->view->page_title = $page->get_title();
        $this->view->langFilter = $data['lang'];
        $this->view->data = $data;
    }
    
    public function deleteAction(){
        $id = $this->_getParam('id');
        
        $id = $this->_getParam('id');
        $route = new Cms_Model_Route();
        if(!Cms_Model_RouteMapper::getInstance()->find($id, $route)){
            $result = array(
                "success" => false,
                "message" => $this->translate("Route not found.")
            );
        }else{
            $success = Cms_Model_RouteMapper::getInstance()->delete($route);
            $result = array(
                        "success" => $success,
                        "message" => ($success)?$this->translate("Route deleted."):$this->translate("Error deleting Route.")
                    );
        }
        return $this->_helper->json($result);
    }
}