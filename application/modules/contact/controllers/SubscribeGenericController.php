<?php
/**
 * Subscribe Generic contact Controller
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */

include_once 'GenericController.php';

class Contact_SubscribeGenericController extends Contact_GenericController {


    
    public function indexAction(){
        $data = $this->getRequest()->getPost('data');
        
        //print_r($this->_fields);
        //create form object
        $form = new Contact_Form_Generic($data,null,$this->_fields, $this->getRequest());

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {
                $values = $form->getValues();
                $subscription = new Contact_Model_Subscription();
                //exist?
                if(!Contact_Model_SubscriptionMapper::getInstance()->findByEmail($values['email'], $subscription)){
                    //persist
                    $subscription->setOptions($values);
                    $subscription       ->set_code(md5(uniqid()))
                                        ->set_status('pending')
                                        ->set_lang(CURR_LANG);
                    Contact_Model_SubscriptionMapper::getInstance()->save($subscription);                    
                }
                $data = $subscription->toArray();
                $data['new_status'] = ($values['subscribe'] == 'yes')? 'subscribed': 'unsubscribed';
                //set custom email subject
                if($data['new_status'] == 'unsubscribed' && isset($this->_formParams['email']['subject_respond_un'])){
                    $this->_formParams['email']['subject_respond'] = $this->_formParams['email']['subject_respond_un'];
                }
                $data['link'] = $this->view->serverUrl() . $this->view->url(array(
                    'module'        => 'contact',
                    'controller'    => 'subscribe-generic',
                    'action'        => 'status',
                    'id'            => $subscription->get_id(),
                    'code'          => $subscription->get_code(),
                    'new_status'  => $data['new_status'],
                    'lang'          => CURR_LANG
                ));
                //send email
                $this->sendContactEmail($data, $this->_fields, CURR_LANG);
                //sending done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array(
                    'module'        => 'contact',
                    'controller'    => $this->_request->getControllerName(),
                    'action'        => 'landing',
                    'status'        => $data['new_status']
                )), $this->translate('Thank you for the submission'));
            }
            else {
                $this->_formHelper->returnError($form->getMessages());
            }
        } else {
            //set default values
            Contact_Form_Generic::setDefaultValues($this->_fields, $data);
        }
        $this->view->data = $data;
        $this->view->fields = $this->_fields;
        $this->view->formParams = $this->_formParams;
        $this->view->formId = $this->_formId;
        if(isset($this->_formParams['template'])){
            $this->renderScript($this->_formParams['template']);
        }
    }
    
    public function landingAction(){
        $status = ('subscribed' == $this->_getParam('status'))? 'subscribed' : 'unsubscribed';
        $this->renderScript('subscribe-generic/' . CURR_LANG . '/landing_' . $status . '.phtml');
    }    
    

    public function statusAction(){
        $id = $this->_getParam('id');
        $code = $this->_getParam('code');
        $status = $this->_getParam('new_status');
        
        //check input
        if(!isset($id)){
            throw new Zend_Exception('Subscription not found');
        }
        if(!isset($code)){
            throw new Zend_Exception('Subscription not found');
        } 
        if(!isset($status) || !in_array($status, array('subscribed','unsubscribed'))){
            throw new Zend_Exception('Subscription not found');
        }        
        
        $subscription = new Contact_Model_Subscription();
        if(!Contact_Model_SubscriptionMapper::getInstance()->findByCode($id, $code, $subscription)){
            throw new Zend_Exception('Subscription not found');
        }
        
        //update status
        if($subscription->get_status() != $status){
            $subscription->set_status($status);
            $dt = date("Y-m-d H:i:s");
            if($status == 'subscribed'){
                $subscription->set_subscribed_dt($dt);
            }
            else{
                $subscription->set_unsubscribed_dt($dt);
            }
            Contact_Model_SubscriptionMapper::getInstance()->save($subscription);
        }
        
        $this->view->subscription = $subscription;
        
        $this->renderScript('subscribe-generic/' . CURR_LANG . '/status_' . $status . '.phtml');
    }

}