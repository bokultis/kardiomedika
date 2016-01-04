<?php

/**
 * Admin controller
 *
 * @package Admin
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Admin_ApplicationController extends HCMS_Controller_Action_Admin {

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

    /**
     * List application
     */
    public function indexAction() {
    }

    /**
     * Ajax listing of applications
     */
    public function listAction() {
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
        if(null != $this->_getParam('statusFilter')){
            $criteria['status'] = $this->_getParam('statusFilter');
        }
        if(null != $this->_getParam('searchFilter')){
            $criteria['search_filter'] = $this->_getParam('searchFilter');
        }
        //order
        $order = $this->_request->getParam('order');
        if (isset($order)) {
            $order = array($order);
        } else {
            $order = array("p.name ASC");
        }

        $records = Application_Model_ApplicationMapper::getInstance()->fetchAll($criteria); 

        /* @var $record Cms_Model_Page */
        foreach ($records as $record) {
            $data['rows'][] = array(
                'id'        => $record->get_id(),
                'name'     => $record->get_name(),
                'status'    => $this->translate($record->get_status()),
                'status_dt'    => HCMS_Utils_Time::timeMysql2Local($record->get_status_dt())
            );
        }

        $this->_helper->json->sendJson($data);
    }

    public function editAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $formTab = $this->getRequest()->getUserParam('tab', "General");
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
        $this->view->emailTransportTypeOptions = Application_Model_Application::getEmailTransportTypeOptions();
        $this->view->data = $application->toArray();
        
        $formClassName = "Admin_Form_Config".$formTab;
        if (!class_exists($formClassName)) {
               $this->_formHelper->returnError("ne postoji"); // Mare Srbine
        }
        $formConfigParams = "";
        $formConfigToEmails = "";
        switch ($formTab) {
            case "Email":
                $formConfigParams = new Admin_Form_ConfigParameters($data['parameters']);
                foreach ($data['to_emails'] as $to_emails) {
                     $formConfigToEmails[] = new Admin_Form_ConfigToEmails($to_emails);
                }
                break;
            case "Upload":
                if(isset($data['upload'])){
                    foreach ($data['upload'] as $upload) {
                            $formConfigUploads[] = new Admin_Form_ConfigUploads($upload);
                    }
                }
                if(isset($data['default_upload'])){
                    foreach ($data['default_upload'] as $defaultUpload) {
                        $formConfigDefaultUploads[] = new Admin_Form_ConfigUploads($defaultUpload);
                    }
                }
                break;
            default :
                break;
        }
        //create form object
        $form = new $formClassName($data);
        $errors= array();
        //postback - save?
        if ($this->_formHelper->isSave()) {  
            //check if valid
            if($form->isValid()) {   
                switch ($formTab) {
                    case "Email": 
                       $email_settings = $application->get_email_settings();
                        unset($email_settings['to_emails']);
                        unset($email_settings['parameters']);
                       $values['email_settings'] = $form->getValues();
//                       print_r(array_diff_assoc($application->get_email_settings(), $form->getValues()));die;
                       if($formConfigParams->isValid()){
                            $values['email_settings']['parameters']  = $formConfigParams->getValues();
                        }else{
                           $errors[] = $formConfigParams->getMessages(); 
                        }
                        foreach ($formConfigToEmails as $formConfigToEmail) {
                            if($formConfigToEmail->isValid()){
                                $values['email_settings']['to_emails'][]=$formConfigToEmail->getValues();
                            }else{
                                $errors[] = $formConfigToEmail->getMessages(); 
                            }
                        }
                        if(count($errors) > 0){
                            $this->_formHelper->returnError($errors[0]);
                        }
                        $values['email_settings'] =  $this->_mergeArrays($email_settings, $values['email_settings']);
                    break;
                    case "Fb":
                        $values['fb_settings'] = $form->getValues();
                        $values['fb_settings']['login_params']['scope'] = $data['login_params']['scope'];
                        $values['fb_settings']['login_params']['redirect_uri'] = $data['login_params']['redirect_uri'];
                    break;
                    case "Twitter":
                        $values['twitter_settings'] = $form->getValues();
                    break;
                    case "Og":
                        $values['og_settings'] = $form->getValues();
                    break;
                    case "Notes":
                        $notes = $form->getValues();
                        $values['settings'] = $application->get_settings();
                        $values['settings']['notes'] = $notes['notes'];
                    break;
                    case "Upload":
                        $values['settings'] = $application->get_settings();
                        unset($values['settings']['upload']);
                        unset($values['settings']['default_upload']);
                        if(isset($formConfigUploads)){
                            foreach ($formConfigUploads as $formConfigUpload) {
                                if($formConfigUpload->isValid()){
                                    $value = $formConfigUpload->getValues();
                                    isset($value['extensions'])?$values['settings']['upload']['extensions'][]=$value['extensions']:'';
                                    isset($value['mimetypes'])? $values['settings']['upload']['mimetypes'][]=$value['mimetypes']:'';
                                }else{
                                    $errors[] = $formConfigUpload->getMessages(); 
                                }
                            }
                        }
                        if(isset($formConfigDefaultUploads)){
                            foreach ($formConfigDefaultUploads as $formConfigDefaultUpload) {
                                if($formConfigDefaultUpload->isValid()){
                                    $value = $formConfigDefaultUpload->getValues();
                                    isset($value['default_extensions'])?$values['settings']['default_upload']['default_extensions'][]=$value['default_extensions']:'';
                                    isset($value['default_mimetypes'])? $values['settings']['default_upload']['default_mimetypes'][]=$value['default_mimetypes']:'';
                                }else{
                                    $errors[] = $formConfigDefaultUpload->getMessages(); 
                                }
                            }
                        }
                        if(count($errors) > 0){
                            $this->_formHelper->returnError($errors[0]);
                        }
                    break;
                    default :
                        $values = $form->getValues();
                        
                        $values['settings'] = $application->get_settings();
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
        $this->view->notice =$this->translate('Currently using the default file extensions and mime types! Here you can add more file extensions and mime types.');
    }

    public function widgetAction(){
        if($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
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


