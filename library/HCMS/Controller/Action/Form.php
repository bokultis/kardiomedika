<?php
/**
 * Form/Ajax specific Controller
 *
 * @package HCMS
 * @subpackage Controller
 * @copyright Horisen
 * @author milan
 */
class HCMS_Controller_Action_Form extends Zend_Controller_Action {
    /**
     * Class constructor
     *
     * The request and response objects should be registered with the
     * controller, as should be any additional optional arguments; these will be
     * available via {@link getRequest()}, {@link getResponse()}, and
     * {@link getInvokeArgs()}, respectively.
     *
     * When overriding the constructor, please consider this usage as a best
     * practice and ensure that each is registered appropriately; the easiest
     * way to do so is to simply call parent::__construct($request, $response,
     * $invokeArgs).
     *
     * After the request, response, and invokeArgs are set, the
     * {@link $_helper helper broker} is initialized.
     *
     * Finally, {@link init()} is called as the final action of
     * instantiation, and may be safely overridden to perform initialization
     * tasks; as a general rule, override {@link init()} instead of the
     * constructor to customize an action controller's instantiation.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);

        if($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
        }
        else{
            $this->view->headScript()->appendFile('/js/ajaxForm.js');
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
        return $this->_helper->json($json);
    }

    /**
     * Normal/ajax error behaviour
     * 
     * @param array $errorMessages
     */
    public function returnError($errorMessages){
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->returnAjaxResult(FALSE, $errorMessages);
        }
        else{
            $this->view->errors = $errorMessages;
            return;
        }
    }

    /**
     * Normal/ajax success behaviour
     *
     * @param string $modulControllerAction "module/controller/action"
     * @param string $message
     * @param mixed $data custom data
     */
    public function returnSuccess($modulControllerAction,$message, $data = null){
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->returnAjaxResult(TRUE, $message,$data);
        }
        else{
            list($modul,$controller,$action) = explode("/", $modulControllerAction);
            return $this->returnThere(
                    $this->_helper->url->url(
                            array(
                                'module'    => $modul,
                                'controller'=> $controller,
                                'action'    => $action
                            )
                    ),
                    $message, HZ_Controller_Action::MSG_INFO, true);
        }
    }

    /**
     * Normal/ajax cancel behaviour
     *
     * @param string $modulControllerAction "module/controller/action"
     * @param string $message
     */
    public function returnCancel($modulControllerAction,$message){
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->returnAjaxResult(FALSE, $message);
        }
        else{
            list($modul,$controller,$action) = explode("/", $modulControllerAction);
            return $this->returnThere(
                    $this->_helper->url->url(
                            array(
                                'module'    => $modul,
                                'controller'=> $controller,
                                'action'    => $action
                            )
                    ),
                    $message, HZ_Controller_Action::MSG_INFO, true);
        }
    }

    /**
     * Is postback for cancel
     * 
     * @return boolean
     */
    public function isCancel(){
        //ajax cannot have cancel
        if($this->getRequest()->isXmlHttpRequest()) {
            return FALSE;
        }
        else{
            $submit = $this->getRequest()->getPost('submit');
            return $this->getRequest()->isPost() && isset ($submit['cancel']);
        }       
    }

    /**
     * Is postback for save
     * 
     * @return boolean
     */
    public function isSave(){
        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->getRequest()->isPost();
        }
        else{
            $submit = $this->getRequest()->getPost('submit');
            return $this->getRequest()->isPost() && isset ($submit['save']);
        }
    }
}
