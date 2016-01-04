<?php
/**
 * Old password validator
 *
 * @package Modules
 * @subpackage Consumer
 * @copyright Horisen
 * @author bane
 */
class Auth_Form_Validator_PasswordSpecialCharacters extends Zend_Validate_Abstract
{
    const PASS_EXISTS = 'INV_REL_SPEC';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::PASS_EXISTS => 'Password must contain at least 1 special character.'
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
    public function __construct(){
        
    }

    public function isValid($value, $context = null){
        $result = true;
        
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value) == 0){            
            $this->_error(self::PASS_EXISTS);
            $result = false;
        }
        
        return $result;
    }
}