<?php
/**
 * Admin Adapter
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author zeka
 */
class Admin_Model_Auth_Adapter implements Zend_Auth_Adapter_Interface {

    const NOT_FOUND_MESSAGE = "Wrong username or password";
    const BAD_PW_MSG = "Wrong username or password";
    const STATUS_NOT_ACTIVE = "Your account is not active";

    /**
     *
     * @var Auth_Model_User
     */
    protected $_admin;

    /**
     *
     * @var string
     */
    protected $_auth;

    /**
     *
     * @var string
     */
    protected $_password;

    /**
     *
     * @var int
     */
    protected $_applicationId;


    public function __construct($applicationId, $auth, $password) {
        $this->_auth = $auth;
        $this->_password = $password;
        $this->_applicationId = $applicationId;
    }

    /**
     * @throws Zend_Auth_Adapter_Exception If authentication can not be establish
     * @return Zend_Auth_Result
     */
    public function authenticate() {
        $this->_admin = new Auth_Model_User();
        //invalid username
        if(!Auth_Model_UserMapper::getInstance()->findByCredentials($this->_auth, $this->_admin)){
            return $this->createResult(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, self::NOT_FOUND_MESSAGE);
        }
        //invalid pass
        if($this->_admin->get_password() != md5($this->_password)){
            return $this->createResult(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, self::BAD_PW_MSG);
        }
        //not active
        if($this->_admin->get_status() != 'active'){
            return $this->createResult(Zend_Auth_Result::FAILURE_UNCATEGORIZED, self::STATUS_NOT_ACTIVE);
        }
        return $this->createResult(Zend_Auth_Result::SUCCESS);
    }


    private function createResult($code, $messages = array()) {

        if (!is_array($messages)) {
            $messages = array($messages);
        }

        return new Zend_Auth_Result($code, $this->_admin, $messages);
    }
}