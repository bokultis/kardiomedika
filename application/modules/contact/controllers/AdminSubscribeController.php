<?php

/**
 * Admin newsletter controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Contact_AdminSubscribeController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;
    
    protected $_formId = 'subscribe';    
    protected $_formParams = array();
    protected $_fields = array();
    protected $_globalSettings = array();    

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
    }


    public function indexAction(){
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->lang = CURR_LANG;
        $this->view->picker = HCMS_Utils_Date::resolveZendLocaleToDatePickerFormat();
    }

    
    /**
     * List ajax action
     */
    public function listAction(){
        
        $page = $this->_request->getParam('page');
        $perPage = $this->_request->getParam('perPage');
        $orderFilter = $this->_request->getParam('order');
        
        $criteria = array(
            'application_id'    => $this->_applicationId
        );
        if (null != $this->_getParam('langFilter')) {
            $criteria['lang'] = $this->_getParam('langFilter');
        }        
        if (null != $this->_getParam('statusFilter')) {
            $criteria['status'] = $this->_getParam('statusFilter');
        }
        if (null != $this->_getParam('genderFilter')) {
            $criteria['gender'] = $this->_getParam('genderFilter');
        }        
        if (null != $this->_getParam('subscribed_from_dt')) {
            $criteria['subscribed_from'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('subscribed_from_dt'));
        }
        if (null != $this->_getParam('subscribed_to_dt')) {
            $criteria['subscribed_to'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('subscribed_to_dt'));
        }
        if (null != $this->_getParam('unsubscribed_from_dt')) {
            $criteria['unsubscribed_from'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('unsubscribed_from_dt'));
        }
        if (null != $this->_getParam('unsubscribed_to_dt')) {
            $criteria['unsubscribed_to'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('unsubscribed_to_dt'));
        }       

        if(isset($orderFilter) && !empty($orderFilter)){
            $orderBy = array($orderFilter);
        }else{
            $orderBy = array('s.subscribed_dt DESC','s.unsubscribed_dt DESC','s.id DESC');
        }
        if(!isset ($page) || $page < 1){
            $page = 1;
        }
        if(!isset ($perPage) || $perPage < 1 || $perPage > 100){
            $perPage = 100;
        }  
        $paging = array('page' => $page,
                        'perPage' => $perPage);

        $records = Contact_Model_SubscriptionMapper::getInstance()->fetchAll($criteria, $orderBy, $paging);

        $data = array(
            'total'     => $paging['total'],
            'page'      => $paging['page'],
            'records'   => $paging['records'],
            'perPage'   => $paging['perPage'],
            'rows'      => array()
        );

        $languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        
        /* @var $record Contact_Model_Subscription */
        foreach ($records as $record) {
            $data['rows'][] = array(
                'id'                    => $record->get_id(),
                'subscribed'            => HCMS_Utils_Time::timeMysql2Local($record->get_subscribed_dt()),
                'unsubscribed'          => HCMS_Utils_Time::timeMysql2Local($record->get_unsubscribed_dt()),
                'status'                => $record->get_status(),
                'first_name'            => $record->get_first_name(),
                'last_name'             => $record->get_last_name(),
                'email'                 => $record->get_email(),
                'gender'                => $this->translate($record->get_gender()),
                'language'              => $languages[$record->get_lang()]['name']
            );
        }
        $this->_helper->json->sendJson($data);
    }
    
    public function editAction(){
        //load params
        $this->_globalSettings = HCMS_Utils::loadThemeConfig('config.php', 'contact');
        $fieldTypes = HCMS_Utils::loadThemeConfig('types.php', 'contact');
        if($this->_request->getParam('form_id')){
            $this->_formId = $this->_request->getParam('form_id');
        }
        if(!isset($this->_globalSettings['forms'][$this->_formId])){            
            throw new Exception("Form not found");
        }
        $this->_formParams = $this->_globalSettings['forms'][$this->_formId];
        $this->_fields = Contact_Form_Generic::getFieldsArr($fieldTypes, $this->_formParams['fields']);
        
        $data = $this->getRequest()->getPost('data');
        $id = $this->_getParam('id');

        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }        

        //create form object
        $form = new Contact_Form_AdminSubscribe($data);

        //postback - save?
        if ($this->_formHelper->isSave()) { 
            //check if valid
            if($form->isValid()) {
                $values = $form->getValues();
                $subscription = new Contact_Model_Subscription($values);
                if(isset($data['id'])){
                    $oldSubscription = new Contact_Model_Subscription();
                    if(Contact_Model_SubscriptionMapper::getInstance()->find($data['id'], $oldSubscription) && $oldSubscription->get_status() != $subscription->get_status()){
                        $dt = date("Y-m-d H:i:s");
                        switch ($subscription->get_status()) {
                            case 'subscribed':
                                $subscription->set_subscribed_dt($dt);
                                break;
                            case 'unsubscribed':
                                $subscription->set_unsubscribed_dt($dt);
                                break;
                            default:
                                break;
                        }                        
                    }                                   
                }             
                Contact_Model_SubscriptionMapper::getInstance()->save($subscription);                    
                //sending done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action'        => 'index')), $this->translate('Saved'));
            }
            else {
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        //first run of the form - grab data from mapper
        elseif(!$this->_formHelper->getRequest()->isPost()) {
            //edit action
            if(isset ($id) && $id > 0) {
                $subscription = new Contact_Model_Subscription();
                if(!Contact_Model_SubscriptionMapper::getInstance()->find($id, $subscription)){
                    throw new Exception("Record not found");
                }
                //fetch data
                $data = $subscription->toArray();
                //populate form with data
                $form->setData($data);
            }
        }        
      
        $this->view->data = $data;
        $this->view->fields = $this->_fields;
        $this->view->formParams = $this->_formParams;
        $this->view->formId = $this->_formId;
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
            'status' => $this->translate('Status'),            
            'first_name' => $this->translate('First Name'),
            'last_name' => $this->translate('Last Name'),
            'gender' => $this->translate('Gender'),
            'email' => $this->translate('Email'),
            'lang' => $this->translate('Language'),
            'subscribed_dt' => $this->translate('Subscribed'),
            'unsubscribed_dt' => $this->translate('Unsubscribed')
        );
        
        $criteria = array(
            'application_id'    => $this->_applicationId,
            'data_type' => 'array'
        );
        
        $criteria = array(
            'application_id'    => $this->_applicationId
        );
        if (null != $this->_getParam('langFilter')) {
            $criteria['lang'] = $this->_getParam('langFilter');
        }        
        if (null != $this->_getParam('statusFilter')) {
            $criteria['status'] = $this->_getParam('statusFilter');
        }
        if (null != $this->_getParam('genderFilter')) {
            $criteria['gender'] = $this->_getParam('genderFilter');
        }        
        if (null != $this->_getParam('subscribed_from_dt')) {
            $criteria['subscribed_from'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('subscribed_from_dt'));
        }
        if (null != $this->_getParam('subscribed_to_dt')) {
            $criteria['subscribed_to'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('subscribed_to_dt'));
        }
        if (null != $this->_getParam('unsubscribed_from_dt')) {
            $criteria['unsubscribed_from'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('unsubscribed_from_dt'));
        }
        if (null != $this->_getParam('unsubscribed_to_dt')) {
            $criteria['unsubscribed_to'] = HCMS_Utils_Date::dateLocalToIso($this->_getParam('unsubscribed_to_dt'));
        }        
        
        if(isset($orderFilter) && !empty($orderFilter)){
            $orderBy = array($orderFilter);
        }else{
            $orderBy = array('s.subscribed_dt DESC','s.unsubscribed_dt DESC','s.id DESC');
        }       


        //read data to export
        $records = Contact_Model_SubscriptionMapper::getInstance()->fetchAll($criteria, $orderBy);
        /* @var $record Contact_Model_Subscription */
        foreach ($records as $record) {
            $recordArr = $record->toArray();
            $recordArr['gender'] = isset($recordArr['gender']) ? $this->view->translate($recordArr['gender']) : '';            
            $records_trans[] = $recordArr;
        }        
        //get exported object
        $objPHPExcel = Contact_Model_SubscriptionMapper::getInstance()->exportToExcel($this->_applicationId, $header, $records_trans, $this);
        if ($objPHPExcel != null) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            //disable layout
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            // rename sheet
            $objPHPExcel->getActiveSheet()->setTitle($this->translate($this->_application->get_name()));
            $fileName = $this->_application->get_name() . "-newsletter-subscriptions" . "-" . Zend_Date::now()->toString('d-MMM-Y') . ".xls";
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
            $this->_redirect($this->view->url(array('action' => 'index')));
        }
    }
}