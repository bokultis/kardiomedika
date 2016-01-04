<?php

/**
 * Admin controller
 *
 * @package Admin
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Cms_AdminConfigController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
    }

    public function configAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $formTab = $this->getRequest()->getUserParam('tab', "Sitemap");
        $id = $this->_applicationId;
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }
        $module = new Application_Model_Module();
         //read contact module
        if(!Application_Model_ModuleMapper::getInstance()->findByCode("cms", $module) ){
            throw new Exception("CMS module not found");
        }
        $this->view->menus = Cms_Model_MenuMapper::getInstance()->getMenus();
        $this->view->data = $module->toArray();
        $formClassName = "Cms_Form_Config".$formTab;
        if (!class_exists($formClassName)) {
               $this->_formHelper->returnError("ne postoji");
        }
        //create form object
        $form = new $formClassName($data);

        //postback - save?
        if ($this->_formHelper->isSave()) { 
            //check if valid
            if($form->isValid()) {
                $setings = $module->get_settings();
                $setings['sitemap'] = $form->getValues();
                //create module entity object
                $module->set_settings(json_encode($setings));           
                $module->set_data(json_encode($module->get_data()));           
                //new entity
                Application_Model_ModuleMapper::getInstance()->save($module);          
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'config')), $this->translate('Configuration saved.'));
            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
//        print_r($data);die;
    }
}


