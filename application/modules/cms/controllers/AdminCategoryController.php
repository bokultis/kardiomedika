<?php

/**
 * Admin Category controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author zeka
 */
class Cms_AdminCategoryController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
    }

    /**
     * List categories
     */
    public function categoryAction() {
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->categorySets = Cms_Model_CategorySetMapper::getInstance()->getCategorySets();
        $this->view->pageTypes = Cms_Model_PageTypeMapper::getInstance()->fetchAll();
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll(array('application_id' => $this->_applicationId));
        $this->view->modules = $modules;
    }

    /**
     * Ajax listing of pages
     */
    public function categoryListAction() {
        //criteria category set id
        $criteria['set_id'] = $this->_getParam('categorySetFilter', '');
        if (null != $this->_getParam('language')) {
            $criteria['lang'] = $this->_getParam('language');
        }
        //order
        $order = $this->_request->getParam('order');
        if (isset($order) && $order != '') {
            $order = array($order);
        } else {
            $order = array('c.level ASC','c.parent_id');
        }
        $records = Cms_Model_CategoryMapper::getInstance()->fetchZendNavigationArray($criteria, $order);

        /* @var $record Cms_Model_Category */
        $data = array(
            'rows' => array()
        );

        $data = $this->createCategoryList($records, $data);
        $this->_helper->json->sendJson($data);
    }

    /**
     * Category edit action
     *
     * @return
     */
    public function categoryEditAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $setId = $this->_getParam('set_id');
        $language = $this->_getParam('language');

        $categorySet = new Cms_Model_CategorySet();
        if(!isset($setId) || !Cms_Model_CategorySetMapper::getInstance()->find($setId, $categorySet)){
            throw new Exception("Category set is not defined!");
        }
        
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'category')), $this->translate('Action canceled'));
        }        
        //create form object
        $form = new Cms_Form_Category($data);
        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();
                $values['parent_id'] == 0 ? $values['level'] = 0 :$values['level'] = $values['parent_id'] + 1;
                //Save category
                $category = new Cms_Model_Category($values); 
                Cms_Model_CategoryMapper::getInstance()->save($category, $language);
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'category')), $this->translate('Category saved.'));
            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        //first run of the form - grab data from mapper
        else if(!$this->_formHelper->getRequest()->isPost()) {
            //show category action
            if(isset ($id) && $id > 0) {
                $category = new Cms_Model_Category();
                if(!Cms_Model_CategoryMapper::getInstance()->find($id, $category, $language)){
                    throw new Exception("Category not found");
                }
                $data = $category->toArray();
                $data['parent_id'] == 0 ? $data['level'] = 0 : $data['level'] = $data['parent_id'] + 1;
                //populate form with data
                $form->setData($data);
                $this->view->data = $data;
            }
        }
        $this->view->categorySet = $categorySet;
        $this->view->data = $data;
    }
    
    public function categoryDeleteAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $data = $this->getRequest()->getPost('data');

        $id = $this->_getParam('id');
        //delete category      
        Cms_Model_CategoryMapper::getInstance()->delete($id);         
        
        $data = array(
                'success'   => true,
                'message'   => $this->translate("Category deleted"),
        );
       
        $this->_helper->json->sendJson($data);
    }

    /**
     * Ajax action to obtain url_id from name
     */
    public function urlIdAction(){
        /* @var $filter Zend_Filter */
        $filter = HCMS_Filter_CharConvert::createSEOFilter(array(), array());
        $this->_helper->json(array(
            'url_id' => $filter->filter($this->_getParam('name'))
        ));
    }

    /**
     * Ajax action to obtain pages types for a module
     */
    public function getModulePageTypesAction(){
        $data = array('success'   => false, 'message'   => $this->translate("No page types associated with this module."), 'page_types'  => '');
        $pageTypes = Cms_Model_PageTypeMapper::getInstance()->findByModule($this->_getParam('id'));
        if(count($pageTypes)){
            $data = array(
                'success'   => true,
                'message'   => $this->translate("Page types obtained."),
                'page_types'  => $pageTypes
            );
        }

        $this->_helper->json->sendJson($data);
        
        
    }
    
    public function setEditAction(){
        $data = $this->getRequest()->getPost('data');       
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'category')), $this->translate('Action canceled'));
        }        
        
        //create form object
        $form = new Cms_Form_CategorySet($data);
        if ($this->_formHelper->isSave()) {          
            if($form->isValid()) {                
                $values = $form->getValues();
                $categorySet = new Cms_Model_CategorySet($values);
                Cms_Model_CategorySetMapper::getInstance()->save($categorySet);
                $data = array(
                    'success'   => true,
                    'set' => Cms_Model_CategorySetMapper::getInstance()->getCategorySets(),
                    'message'   => $this->translate("New Category added"),
                    'selected'  => $categorySet->get_id());
               
                $this->_helper->json->sendJson($data);
            }else{
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll(array('application_id' => $this->_applicationId));
        $this->view->modules = $modules;
        
    }

    //------------------------ PRIVATE METHODS ----------------------//

    /**
     * Create category list
     *
     * @param array $records
     * @param array $data
     * @return array
     */
    private function createCategoryList($records, &$data){

        foreach ($records as $key => $record) {
            $data['rows'][] = array(
                'id'    => $record['id'],
                'name'  => $record['name'],
                'description'  => $record['description'],
                'level' => $record['level']
            );
            if(isset($record['pages']) && count($record['pages']) > 0){
                $this->createCategoryList($record['pages'], $data);
            }
        }
        return $data;
    }
}