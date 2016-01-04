<?php

/**
 * Admin Menu controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_AdminMenuController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;
    
    protected $_defaultLang = null;
    
    protected $_authPrivilege = 'master';
    
    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
        $this->_defaultLang = Application_Model_TranslateMapper::getInstance()->getDefaultLang();
    }

    /**
     * List menu items
     */
    public function menuAction() {
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->menus = Cms_Model_MenuMapper::getInstance()->getMenus();
        $this->view->defaultLang = $this->_defaultLang;
    }

    /**
     * Ajax listing of menu items
     */
    public function menuListAction() {
        //criteria
        $criteria = array(
            'application_id' => $this->_applicationId
        );
        if (null != $this->_getParam('langFilter')) {
            $criteria['lang'] = $this->_getParam('langFilter');
        }
        
        if(null != $this->_getParam('searchFilter')){
            $criteria['search_filter'] = $this->_getParam('searchFilter');
        }
        if(null != $this->_getParam('menuFilter')){
            $criteria['menu'] = $this->_getParam('menuFilter');
        }else{
            $criteria['menu'] = 'main';
        }
        
        //order
//        $order =  $this->_request->getParam('order');
        if (isset($order)) {
            $order = array($order);
        } else {
            $order = array('mi.level ASC','mi.parent_id','mi.ord_num');
        }
        $records = Cms_Model_MenuItemMapper::getInstance()->fetchZendNavigationArray($criteria, $order);

        /* @var $record Cms_Model_MenuItem */
        $data = array(
            'rows' => array()
        );
        $data = $this->createMenuList($records, $data);
        
        $this->_helper->json->sendJson($data);
    }
    
    private function createMenuList($records, &$data){
        foreach ($records as $key => $record) {
            $item = $record['entity'];
            $lang = (null != $this->_getParam('langFilter'))? $this->_getParam('langFilter'): Application_Model_TranslateMapper::getInstance()->getDefaultLang();
            
            if(!is_null($item->get_page_id())){
                //default path
                $module = 'cms';
                $controller = 'page';
                $action = 'index';
            }
            else{
                $mca =explode("/", $item->get_path()) ;
                $module =(isset($mca[0]))? $mca[0]:"";
                $controller =(isset($mca[1]))? $mca[1]:"";
                $action =(isset($mca[2]))? $mca[2]:"";
            }
            $urlParams = array(
                'module'        => $module,
                'controller'    => $controller,
                'action'        => $action,
                'page_id'       => $item->get_page_id(),
                'lang'          => $lang
            );
            $data['rows'][] = array(
                'id' => $item->get_id(),
                'name' => $item->get_name(),
                'level' => $item->get_level(),
                'hidden' => $item->get_hidden(),
                'parent_id' => $item->get_parent_id(),
                'ord_num' => $item->get_ord_num(),
                'url'   => $this->view->url($urlParams,'cms',true)
            );
            if(count($record['pages']) > 0){
                $this->createMenuList($record['pages'], $data);
            }
        }
        return $data;
    }


    public function menuEditAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $langFilter = $this->_getParam('langFilter');        
        $menuFilter = $this->_getParam('menuFilter');        
        
        /* get routes from application.ini */
        $bootstrap = $this->getInvokeArg('bootstrap');
        $routes =  array_keys($bootstrap->getResource('router')->getRoutes());
        
        /* get all modules */
        $criteria['application_id'] = $this->_applicationId;
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll($criteria);
       
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'menu')), $this->translate('Action canceled'));
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
        //create form object
        $form = new Cms_Form_MenuItem($data);
        $route = new Cms_Model_Route();         
        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();
                
                //create entity object from submitted values, and save
                $item = new Cms_Model_MenuItem($values);                
                if($data['route'] == ''){
                    $item->set_path(''); 
                    $item->set_page_id(''); 
                    $item->set_page_id_new(''); 
                    $item->set_params(''); 
                }else{
                    $item->set_uri(''); 
                }
                //new entity
                if(!isset ($data['id']) || $data['id'] <= 0){
                    $item->set_application_id($this->_applicationId);
                }
                else{                    
                    $existingMenu = new Cms_Model_MenuItem();
                    if(!Cms_Model_MenuItemMapper::getInstance()->find($data['id'], $existingMenu)){
                        throw new Exception("Menu not found");
                    }
                    if((int)$existingMenu->get_application_id() != $this->_applicationId){
                        throw new Exception("Cannot edit this Menu Item.");
                    }                    
                } 
                $item->set_application_id($this->_applicationId);
                Cms_Model_MenuItemMapper::getInstance()->save($item,$langFilter);                
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'menu')), $this->translate('Menu Item saved.'));
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
                $item = new Cms_Model_MenuItem();
                if(!Cms_Model_MenuItemMapper::getInstance()->find($id, $item, $langFilter)){
                    throw new Exception("Menu not found");
                }
                Cms_Model_RouteMapper::getInstance()->findByPath($item->get_path(), $this->_applicationId, $route, $item->get_params(), ($langFilter != '')?$langFilter:null);
                $item->set_route_uri($route->get_uri());
                $params= $item->get_params();
                $item->set_params(Cms_Model_MenuItemMapper::getInstance()->unsetParamsPageId($params));
                
                //fetch data
                $data = $item->toArray();
                //populate form with data
                $form->setData($data);
            }
        }
        $this->view->menus = Cms_Model_MenuMapper::getInstance()->getMenus();
        $this->view->menuFilter = $menuFilter;
        $this->view->langFilter = $langFilter;
        $this->view->routes = $routes;
        $this->view->modules = $modules;
        $page = new Cms_Model_Page();
        if(isset($data['page_id']))
            Cms_Model_PageMapper::getInstance()->find($data['page_id'], $page);
        $this->view->page_title = $page->get_title();
        $this->view->data = $data;
    }
    
    public function editMainMenuAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        //create form object
        $form = new Cms_Form_Menu($data);
        if($form->isValid()) {                
            $values = $form->getValues();
            $menu = new Cms_Model_Menu($values); 
            Cms_Model_MenuMapper::getInstance()->save($menu);
        }else{
            //we have errors - return json or continue
            $this->_formHelper->returnError($form->getMessages());
        }
        
        $data = array('menu' => Cms_Model_MenuMapper::getInstance()->getMenus(),
                'success'   => true,
                'message'   => $this->translate("New Menu added"),
                'selected' =>$menu->get_code());
       
        $this->_helper->json->sendJson($data);
    }
    
    
    public function menuDeleteAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        //create form object        
        Cms_Model_MenuItemMapper::getInstance()->delete($id);         
        
        $data = array('menu' => Cms_Model_MenuMapper::getInstance()->getMenus(),
                'success'   => true,
                'message'   => $this->translate("New Menu added"),
        );
       
        $this->_helper->json->sendJson($data);
    }
    
    public function importAction(){    
        if ($this->getRequest()->isPost()){
            try{
                $adapter = new Zend_File_Transfer_Adapter_Http();
                $adapter->addValidator("Count",false, array("min"=>1, "max"=>1))
                        ->addValidator("Size",false,array("max"=>1000000))
                        ->addValidator("Extension",false,array("extension"=>"xlsx", "case" => true));

                $adapter->setDestination(APPLICATION_PATH . "/../tmp/");

                $files = $adapter->getFileInfo();
                foreach($files as $fieldname=>$fileinfo){
                    if (($adapter->isUploaded($fileinfo["name"]))&& ($adapter->isValid($fileinfo['name']))){
                        $extension = substr($fileinfo['name'], strrpos($fileinfo['name'], '.') + 1);
                        $filename = 'file_'.date('Ymdhs').'.'.$extension;
                        $adapter->addFilter('Rename',array('target'=>APPLICATION_PATH . "/../tmp/".$filename,'overwrite'=>true));
                        $adapter->receive($fileinfo["name"]);
                    }
                }
                if(count($adapter->getMessages()) > 0 ){
                    return $this->returnAjaxResult(FALSE, $adapter->getMessages());
                } else {
                    $errors = array();
                    $files = $adapter->getFileInfo();
                    $importer = new Cms_Model_SiteMapImporter();                                        
                    foreach ($files as $file){
                        $result = $importer->importXls($file['destination']."/".$file['name']);
                        if(!$result){
                            $errors = $importer->getErrors();
                        }
                    }                    
                    if(count($errors) > 0){
                        foreach ($errors as $error){
                            $message[]= $error;
                        }
                        return $this->returnAjaxResult(FALSE, $message);
                    }else{
                        return $this->returnAjaxResult(TRUE, $this->translate('Sitemap successfully imported.'));
                    }
                }
            }
            catch (Exception $ex){                
               return $this->returnAjaxResult(FALSE, $ex->getMessage());
            }
        } else {
            return $this->returnAjaxResult(FALSE, "No file");
        }
        die();
    }  
    
    private function returnAjaxResult($success,$message){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);        
        echo json_encode(array(
            'success'   => $success,
            'message'   => $message
        ));
    }
}