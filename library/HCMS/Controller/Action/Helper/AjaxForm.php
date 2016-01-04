<?php
/**
 * Ajax Form action helper
 * 
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */

/**
 * Helper for creating forms
 */
class HCMS_Controller_Action_Helper_AjaxForm extends Zend_Controller_Action_Helper_Abstract
{
    const MSG_OK = "ok";
    const MSG_ERR = "err";
    const MSG_INFO = "info";
    const MSG_WARN = "warn";

    public function init(){
        if(isset ($this->_actionController) && $this->_actionController->getRequest()->isXmlHttpRequest()) {
            $layout = $this->_actionController->getHelper('layout');
            if(isset ($layout)){
                $layout->disableLayout();
            }
        }
        else{
            if(isset ($this->_actionController->view)){
                $this->_actionController->view->headScript()->appendFile('/js/ajaxForm.js');
            }
        }
    }
    
    /**
     * Return ajax result
     *
     * @param boolean $success
     * @param array|string $message
     * @param mixed $data custom data
     */
    private function returnAjaxResult($success,$message,$data = null){
        $json = array(
            'success'   => $success,
            'message'   => $message
        );
        if(isset ($data)){
            $json['data'] = $data;
        }
        return $this->_actionController->getHelper('json')->direct($json);
    }

    /**
     * Normal/ajax error behaviour
     *
     * @param array $errorMessages
     */
    public function returnError($errorMessages){
        if($this->_actionController->getRequest()->isXmlHttpRequest()) {
            return $this->returnAjaxResult(FALSE, $errorMessages);
        }
        else{
            $this->_actionController->view->errors = $errorMessages;
            return;
        }
    }

    /**
     * Add flash message - text,type - look this class consts
     *
     * @param string $message
     * @param string $messageType
     */
    public function addMessage($message,$messageType){
        $this->_actionController->getHelper('flashMessenger')->addMessage(array($messageType=>$message));
    }

    protected function _redirectMsg($url,$message,$messageType){
        if($message && $messageType){
            $this->addMessage($message, $messageType);
        }
        /* @var $redirector Zend_Controller_Action_Helper_Redirector */
        $redirector = $this->_actionController->getHelper('Redirector');
        //error_log("redirecting to " . $redirectUrl . " with $prependBase = $prependBase" );
        $redirector->gotoUrlAndExit($url);
    }

    /**
     * Normal/ajax success behaviour
     *
     * @param string $url "module/controller/action"
     * @param string $message
     * @param mixed $data custom data
     */
    public function returnSuccess($url,$message,$data = null){
        if($this->_actionController->getRequest()->isXmlHttpRequest()) {
            return $this->returnAjaxResult(TRUE, $message,$data);
        }
        else{
            $this->_redirectMsg($url, $message, self::MSG_OK);
        }
    }

    /**
     * Normal/ajax cancel behaviour
     *
     * @param string $url "module/controller/action"
     * @param string $message
     */
    public function returnCancel($url,$message){
        if($this->_actionController->getRequest()->isXmlHttpRequest()) {
            return $this->returnAjaxResult(FALSE, $message);
        }
        else{
            $this->_redirectMsg($url, $message, self::MSG_INFO);
        }
    }

    /**
     * Is postback for cancel
     *
     * @return boolean
     */
    public function isCancel(){
        //ajax cannot have cancel
        if($this->_actionController->getRequest()->isXmlHttpRequest()) {
            return FALSE;
        }
        else{
            $submit = $this->_actionController->getRequest()->getPost('submit');
            return $this->_actionController->getRequest()->isPost() && isset ($submit['cancel']);
        }
    }

    /**
     * Is postback for save
     *
     * @return boolean
     */
    public function isSave(){
        if($this->_actionController->getRequest()->isXmlHttpRequest()) {
            return $this->_actionController->getRequest()->isPost();
        }
        else{
            $submit = $this->_actionController->getRequest()->getPost('submit');
            return $this->_actionController->getRequest()->isPost() && isset ($submit['save']);
        }
    }
}