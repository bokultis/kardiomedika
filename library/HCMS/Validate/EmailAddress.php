<?php
/**
 * Email Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author milan
 */
class HCMS_Validate_EmailAddress extends Zend_Validate_EmailAddress
{

    const INVALID            = 'singleEmailAddressInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID     => "'%value%' is no valid email address",
    );

    /**
     * Instantiates hostname validator for local use
     *
     * The following option keys are supported:
     * 'hostname' => A hostname validator, see Zend_Validate_Hostname
     * 'allow'    => Options for the hostname validator, see Zend_Validate_Hostname::ALLOW_*
     * 'mx'       => If MX check should be enabled, boolean
     * 'deep'     => If a deep MX check should be done, boolean
     *
     * @param array|Zend_Config $options OPTIONAL
     * @return void
     */
    public function __construct($options = array()){
        parent::__construct($options);
    }

    public function isValid($value, $context = null){
        if(parent::isValid($value)){
            return true;
        }
        $this->_messages = array();
        $this->_errors   = array();
        $this->_error(self::INVALID);
        return false;
    }

    /**
     * Sets the validation failure message template for a particular key
     *
     * @param  string $messageString
     * @param  string $messageKey     OPTIONAL
     * @return Zend_Validate_Abstract Provides a fluent interface
     * @throws Zend_Validate_Exception
     */
    public function setMessage($messageString, $messageKey = null)
    {
        if ($messageKey === null) {
            $keys = array_keys($this->_messageTemplates);
            foreach($keys as $key) {
                $this->setMessage($messageString, $key);
            }
            return $this;
        }

        if (!isset($this->_messageTemplates[$messageKey])) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("No message template exists for key '$messageKey'");
        }

        $this->_messageTemplates[$messageKey] = $messageString;
        return $this;
    }
}