<?php

/**
 * Google service controller
 *
 * @package Admin
 * @subpackage Controllers
 * @copyright Horisen
 * @author boris
 */
class Admin_GoogleServiceController extends HCMS_Controller_Action_Admin {

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
        $formTab = $this->getRequest()->getUserParam('tab', "Gsc");
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
                    case "Gsc": 

                        $values['settings']['gsc']['name'] = $formValues['name'];
                        $values['settings']['gsc']['cx'] = $formValues['cx'];
                        $values['settings']['gsc']['active'] = $formValues['active'] == "true" ? true:false;

                        $values['settings']['gsc']['css']['.gs-title']['color'] = $formValues['title-color'];
                        $values['settings']['gsc']['css']['.gs-title']['font-size'] = $formValues['title-font-size'];

                        $values['settings']['gsc']['css']['.gs-bidi-start-align']['color'] = $formValues['snippet-color'];
                        $values['settings']['gsc']['css']['.gs-bidi-start-align']['font-size'] = $formValues['snippet-font-size'];

                        $values['settings']['gsc']['css']['.gs-visibleUrl']['color'] = $formValues['visible-url-color'];
                        $values['settings']['gsc']['css']['.gs-visibleUrl']['font-size'] = $formValues['visible-url-font-size'];

                        break;
                    case "Ga":
                        $values['settings']['tags']['ga']['tracking_id'] = $formValues['tracking_id'];
                        $values['settings']['tags']['ga']['active'] = $formValues['ga_active'] == "true" ? true:false;
                        
                        break;
                    case "WMT":
                        $values['settings']['tags']['wmt']['meta'] = $formValues['wmt_meta'];
                        $values['settings']['tags']['wmt']['active'] = $formValues['wmt_active']  == "true" ? true:false;
                        
                        break;
                    case "GTM":
                        $values['settings']['tags']['gtm']['container_id'] = $formValues['container_id'];
                        $values['settings']['tags']['gtm']['active'] = $formValues['gtm_active'] == "true" ? true:false;
                        
                        break;
                    case "Robots":
                        $values['settings']['tags']['robots']['active'] = $formValues['robots_active'] == "true" ? true:false;
                        
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


