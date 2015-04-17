<?php

class ErrorController extends HCMS_Controller_Action_Cms {

    public function init(){
        //cli version
        if (PHP_SAPI != 'cli') {
            parent::init();
        }        
    }    

    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(400);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if (false !== ($log = $this->getLog())) {
            $errorLevel = Zend_Log::ERR;
            if($this->getResponse()->getHttpResponseCode() == 404) {
                $errorLevel = Zend_Log::NOTICE;
            }
            /*@var $log Zend_Log */
            $log->log(  $errors->exception .
                        //include additional info
                        "\n\nrequest_uri: " . $_SERVER['REQUEST_URI'] . "\n" .
                        "client ip: " . $this->getIP(),
                        $errorLevel);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
        //cli version
        if (PHP_SAPI == 'cli') {
            echo "An error occurred\n";
            echo $this->view->message . "\n";
            // conditionally display exceptions
            if ($this->getInvokeArg('displayExceptions') == true) {
                echo $errors->exception->getMessage() . "\n";
                echo "\nStack Trace:\n" . $errors->exception->getTraceAsString();
            }
            die();
        }
        //ajax version
        if (method_exists($this->getRequest(), "isXmlHttpRequest") && $this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->contextSwitch()->initJsonContext();
            $response = array('success' => false);
            if ($this->getInvokeArg('displayExceptions') == true) {
                // Add exception error message
                $response['message'] = $errors->exception->getMessage();
                // Send stack trace
                $response['trace'] = $errors->exception->getTrace();
                // Send request params
                $response['request'] = $this->getRequest()->getParams();
            }
            else {
                $response['message'] = "An application error occured.";
            }
            return $this->getHelper('json')->direct($response);
        }
        
    }

    /**
     * Get client IP
     *
     * @return string
     */
    function getIP() {
        $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if(getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if(getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "UNKNOWN";
        return $ip;
    }

    /**
     *
     * @return Zend_Log
     */
    public function getLog() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

