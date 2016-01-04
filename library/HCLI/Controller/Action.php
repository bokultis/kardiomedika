<?php

/**
 * Controller action
 *
 * @package HCLI
 * @subpackage Controller
 * @author milan
 *
 */
class HCLI_Controller_Action extends Zend_Controller_Action {
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
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        if (!$request instanceof HCLI_Controller_Request_Cli) {
            throw new Exception("CliController may only be accessed from the command line");
        }
        parent::__construct($request, $response, $invokeArgs);
    }

    /**
     *
     * @return Zend_Console_Getopt
     */
    protected function _getConsoleGetOpt(){
        return $this->getInvokeArg('bootstrap')->getGetOpt();
    }

    /**
     *
     * @return Zend_Console_Getopt
     */
    public function getConsoleOptions(array $rules) {
        $this->getInvokeArg('bootstrap')->addOptionRules($rules);        
        $consoleOptions =  $this->_getConsoleGetOpt();
        return $consoleOptions;
    }
}
