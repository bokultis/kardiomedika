<?php

/**
 * Google analytics controller
 *
 * @package Admin
 * @subpackage Controllers
 * @copyright Horisen
 * @author boris
 */
class Admin_GoogleAnalyticsController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;
    
    protected $_authPrivilege = 'master';

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
    }

    public function editAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $formTab = $this->getRequest()->getUserParam('tab', "Ga");
        $id = $this->_applicationId;
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }
        
        $application = new Application_Model_Application();
        if(!Application_Model_ApplicationMapper::getInstance()->find($id, $application)){
            throw new Exception("Configuration not found");
        }
        
        $this->view->data = $application->toArray();
        $bootstrap = $this->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        $this->view->clientId = isset($config['googleapi']['analitycs']['clientId']) && $config['googleapi']['analitycs']['clientId'] != '' ? $config['googleapi']['analitycs']['clientId']:'';
        
        $formClassName = "Admin_Form_Config".$formTab;
        if (!class_exists($formClassName)) {
               $this->_formHelper->returnError("ne postoji"); // Mare Srbine
        }
       
        //create form object
        $form = new $formClassName($data);
        $errors= array();
        //postback - save?
        if ($this->_formHelper->isSave()) {  
            //check if valid
            if($form->isValid()) { 
                $values['settings'] = $application->get_settings();
                $formValues = $form->getValues();
                switch ($formTab) {
                    case "Ga":
                        $values['settings']['tags']['ga']['tracking_id'] = $formValues['tracking_id'];
                        $values['settings']['tags']['ga']['active'] = $formValues['ga_active'] == "true" ? true:false;
                        break;
                    case "GaView":
                        $values['settings']['tags']['ga']['view_id'] = $formValues['view_id'];
                        break;
                }
                //create entity object from submitted values, and save
                $application = new Application_Model_Application($values);
                $application->set_id($this->_applicationId); 
                //new entity
                Application_Model_ApplicationMapper::getInstance()->save($application);                       
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'index')), $this->translate('Configuration saved.'));
            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        
    } 
    
    private function _mergeArrays($Arr1, $Arr2){
        foreach($Arr2 as $key => $Value){
            if(array_key_exists($key, $Arr1) && is_array($Value))
            $Arr1[$key] = $this->_mergeArrays($Arr1[$key], $Arr2[$key]);
        else
            $Arr1[$key] = $Value;
        }
        return $Arr1;
    }
}


