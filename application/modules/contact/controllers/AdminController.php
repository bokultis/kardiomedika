<?php

/**
 * Admin controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan, miljan, boris
 */
class Contact_AdminController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
    }


    public function contactAction(){
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->lang = (string)Zend_Registry::get('Zend_Locale')->getLanguage();
    }

    
    /**
     * List action
     */
    public function contactListAction(){
        
        $page = $this->_request->getParam('page');
        $perPage = $this->_request->getParam('perPage');
        $langFilter = $this->_request->getParam('langFilter');
        $searchFilter = $this->_request->getParam('searchFilter');
        $fromFilter = $this->_request->getParam('fromFilter');
        $toFilter = $this->_request->getParam('toFilter');
        $orderFilter = $this->_request->getParam('order');

        if(isset($fromFilter) && !empty($fromFilter))$fromFilter = HCMS_Utils_Date::dateLocalToCustom($fromFilter); 
        if(isset($toFilter) && !empty($toFilter))$toFilter = HCMS_Utils_Date::dateLocalToCustom($toFilter);

        if(isset($orderFilter) && !empty($orderFilter)){
            $orderBy = array($orderFilter);
        }else{
            $orderBy = array('c.posted DESC');
        }
        if(!isset ($page) || $page < 1){
            $page = 1;
        }
        if(!isset ($perPage) || $perPage < 1 || $perPage > 100){
            $perPage = 100;
        }  
        $paging = array('page' => $page,
                        'perPage' => $perPage);
        $criteria = array(
            'application_id'    => $this->_applicationId
        );
        if(isset ($searchFilter) && $searchFilter != ""){
            $criteria['search_filter'] = $searchFilter;
        }

        if(isset ($langFilter) && $langFilter != ""){
            $criteria['lang_filter'] = $langFilter;
        }

        if(isset ($fromFilter) && $fromFilter != ""){
            $criteria['from_filter'] = $fromFilter;
        }

        if(isset ($toFilter) && $toFilter != ""){
            $criteria['to_filter'] = $toFilter;
        }

        $records = Contact_Model_ContactMapper::getInstance()->fetchAll($criteria, $orderBy, $paging);

        $data = array(
            'total'     => $paging['total'],
            'page'      => $paging['page'],
            'records'   => $paging['records'],
            'perPage'   => $paging['perPage'],
            'rows'      => array()
        );

        $languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        
        /* @var $record Application_Model_Candidate */
        foreach ($records as $record) {
            $data['rows'][] = array(
                'id'                    => $record->get_id(),
                'posted'                => HCMS_Utils_Time::timeMysql2Local($record->get_posted()),
                'first_name'            => $record->get_first_name(),
                'last_name'             => $record->get_last_name(),
                'email'                 => $record->get_email(),
                'street'               => $record->get_street(),
                'country'               => $record->get_country(),
                'phone'               => $record->get_phone(),
                'mobile'               => $record->get_mobile(),
                'fax'               => $record->get_fax(),
                'zip'                 => $record->get_zip(),
                'city'                => $record->get_city(),
                'gender'               => $this->translate($record->get_gender()),
                'description'           => $record->get_description(),
                'description_short'     => $this->view->abbreviate($record->get_description(), 150),
                'message'               => $record->get_message(),
                'message_short'         => $this->view->abbreviate($record->get_message(), 150),
                'language'              => $languages[$record->get_language()]['name']
            );
        }
        $this->_helper->json->sendJson($data);
    }


    /**
     * Export action
     */
    public function exportAction() {

        $langFilter = $this->_request->getParam('langFilter');
        $searchFilter = $this->_request->getParam('searchFilter');
        $fromFilter = $this->_request->getParam('fromFilter');
        $toFilter = $this->_request->getParam('toFilter');

        //contact types
        $header = array(
                        'posted' => $this->translate('Posted'),
                        'gender' => $this->translate('Gender'),
                        'first_name' => $this->translate('First Name'),
                        'last_name' => $this->translate('Last Name'),
                        'street' => $this->translate('Street/Nr'),
                        'zip' => $this->translate('Zip'),
                        'city' => $this->translate('City'),
                        'country' => $this->translate('Country'),
                        'email' => $this->translate('Email'),
                        'phone' => $this->translate('Phone'),
                        'mobile' => $this->translate('Mobile'),
                        'fax' => $this->translate('Fax'),
                        'description' => $this->translate('Description'),
                        'language' => $this->translate('Language')
                        );
        
        $criteria = array(
            'application_id'    => $this->_applicationId,
            'data_type' => 'array'
        );
        if(isset($orderFilter)){
            $orderBy = array($orderFilter);
        }else{
            $orderBy = array('c.posted DESC');
        }
        
        if(isset ($searchFilter) && $searchFilter != ""){
            $criteria['search_filter'] = $searchFilter;
        }

        if(isset ($langFilter) && $langFilter != ""){
            $criteria['lang_filter'] = $langFilter;
        }

        if(isset ($fromFilter) && $fromFilter != ""){
            $criteria['from_filter'] = $fromFilter;
        }

        if(isset ($toFilter) && $toFilter != ""){
            $criteria['to_filter'] = $toFilter;
        }


        //read data to export
        $records = Contact_Model_ContactMapper::getInstance()->fetchAll($criteria, $orderBy);
        
        foreach ($records as $record) {
            $record['gender'] = $this->translate($record['gender']);
            $records_trans[] = $record;
        }
        //get exported object
        $objPHPExcel = Contact_Model_ContactMapper::getInstance()->exportToExcel($this->_applicationId, $header, $records_trans, $this);
        if ($objPHPExcel != null) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            //disable layout
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            // rename sheet
            $objPHPExcel->getActiveSheet()->setTitle($this->translate($this->_application->get_name()));
            $fileName = $this->_application->get_name() . "-contact" . "-" . Zend_Date::now()->toString('d-MMM-Y') . ".xls";
            // redirect output to client browser
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            //create excel writer
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // output to browser
            $objWriter->save('php://output');
            exit();
        } else {
            $this->getHelper('flashMessenger')->addMessage(array("err" => $this->translate("Error occurred while exporting!")));
            $this->_redirect($this->view->url(array('action' => 'contact')));
        }
    }

    /**
     * Configuration action
     */
    public function configAction() {
        //read data from request
        $data = $this->getRequest()->getPost('data');
        
        $inputHashChangePost = $this->_request->getParam('inputHashChangePost');
        $inputHashChangeGet = $this->_request->getParam('inputHashChangeGet');
        
              
        
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }

        if($this->getRequest()->getParam("type") == 'contact'){
            $module_name = "contact";
        } else if($this->getRequest()->getParam("type") == 'newsletter'){
            $module_name = "newsletter";           
        } else if($this->getRequest()->getParam("type") == 'blog'){
            $module_name = "blog";
        } else {
            throw new Exception("Contact module not found");
        }
        
        //read contact module
        $module = new Application_Model_Module();
        if(!Application_Model_ModuleMapper::getInstance()->findByCode($module_name, $module) ){
            throw new Exception("Contact module not found");
        }
        
        $this->view->data = array('email' => $module->get_settings('email'));

        $this->view->data['type'] = $module_name;
        $this->view->emailTransportTypeOptions = Application_Model_Application::getEmailTransportTypeOptions();

        //postback - save?
        if ($this->_formHelper->isSave()) {
            
            $formToEmails = new Contact_Form_ConfigToEmailsWrapper($data['email']['to_emails']);
            $formParameters = new Contact_Form_ConfigParameters($data['email']['parameters'], null, $data['email']['transport']);   
            if($module_name == 'contact'){
                $formEmail = new Contact_Form_ConfigEmail($data['email']);
            }elseif($module_name == 'newsletter'){
                $formEmail = new Contact_Form_ConfigEmailNewsletter($data['email']);
            }elseif($module_name == 'blog'){
                $formEmail = new Blog_Form_ConfigEmail($data['email']);
            }
            
            //check if valid
            if( $formEmail->isValid() && $formParameters->isValid() && $formToEmails->isValid() ) {
                $data['email'] = $formEmail->getValues();
                $data['email']['parameters'] = $formParameters->getValues();
                $data['email']['to_emails'] = $formToEmails->getValues(); 
                
                $lang = new Translation_Model_Lang();
                if(!Translation_Model_LangMapper::getInstance()->findByCode(CURR_LANG, $lang)){
                    throw new Exception('No language for this code.'); 
                };
                Translation_Model_TranslationMapper::getInstance()->save('mailtextRespondContactTranslationkey', $lang->get_id(), $data['email']['mailtext_respond_contact'], 'global');

                $data['email']['mailtext_respond_contact'] = 'mailtextRespondContactTranslationkey';
                
                Translation_Model_TranslationMapper::getInstance()->save('landingPageText', $lang->get_id(), $data['email']['landing_page_text'], 'global');
                $data['email']['landing_page_text'] = 'landingPageText';
                
                Translation_Model_TranslationMapper::getInstance()->save('subjectContactTranslationkey', $lang->get_id(), $data['email']['subject_contact'], 'global');
                $data['email']['subject_contact'] = 'subjectContactTranslationkey';
                
                //create module entity object
                $settings = $module->get_settings();
                $settings['email'] = $data['email'];
                $module->set_settings(json_encode($settings));
                $module->set_data(json_encode($module->get_data()));
                //new entity
                Application_Model_ModuleMapper::getInstance()->save($module);
                         
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'config', 'inputHashChangeGet'=>$inputHashChangePost)), $this->translate('Configuration saved.'));
            }else {  
                //we have errors - return json or continue
                $messages = $formEmail->getMessages();
                $messages['parameters'] = $formParameters->getMessages();
                $messages['to_emails'] = $formToEmails->getMessages();
               
                $this->view->data = $data; 
                $this->_formHelper->returnError($messages);
            }
        }
        if(isset($inputHashChangeGet) && $inputHashChangeGet != ''){
            $this->view->inputHashChange = $inputHashChangeGet;
        }else{
            $this->view->inputHashChange = "email-config";
        }
        
        //country assign
        $countries = new Application_Model_Country();
        $countries = Application_Model_CountryMapper::getInstance()->getAllCountries(CURR_LANG);
        $this->view->countries = $countries;
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->fields = Contact_Form_Contact::getFields();
        
    }

     public function contactDeleteAction(){
        $id = $this->_getParam('contact_id');
        $contact = new Contact_Model_Contact();
        if(!Contact_Model_ContactMapper::getInstance()->find($id, $contact)){
            return $this->_formHelper->returnError($this->translate('Contact not found.'));
        }
        Contact_Model_ContactMapper::getInstance()->delete($contact);
        return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'contact-list')), $this->translate('Contact deleted.'));
    }

}
/**
 * 
 */