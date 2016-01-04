<?php
/**
 * PasswordHistory validator
 *
 * @package Modules
 * @subpackage Consumer
 * @copyright Horisen
 * @author bane
 */
class Auth_Form_Validator_PasswordHistory extends Zend_Validate_Abstract
{
    const PASS_EXISTS = 'INV_REL_INT';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::PASS_EXISTS => 'You can not repeat one of your last 13 passwords again.'
    );


    /**
     * data
     *
     * @var mixed
     */
    protected $_data;

    /**
     * value
     *
     * @var mixed
     */
    protected $_value;

    /**
     * Sets validator options
     *
     * @param  array $data
     * @param string $lang
     * @return void
     */
    public function __construct($data){
        $this->_data = $data;
    }

    public function isValid($value, $context = null){
        $result = true;
        
        $isPassedPassword = Auth_Model_UserMapper::getInstance()->checkPassedUserPaswwords($this->_data['id'], $value);
        
        if($isPassedPassword){
            $this->_error(self::PASS_EXISTS);
            $result = false;
        }
        
        return $result;
    }
}