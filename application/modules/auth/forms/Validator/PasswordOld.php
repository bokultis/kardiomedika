<?php
/**
 * Old password validator
 *
 * @package Modules
 * @subpackage Consumer
 * @copyright Horisen
 * @author bane
 */
class Auth_Form_Validator_PasswordOld extends Zend_Validate_Abstract
{
    const PASS_EXISTS = 'INV_REL_INT';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::PASS_EXISTS => 'Your Password is the sane as your Old Password'
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
        $this->_value = $value;
        $user = new Auth_Model_User();
        if(Auth_Model_UserMapper::getInstance()->find($this->_data['id'], $user)){            
            $old_password = $user->get_password();
            if(md5($this->_data['old_password']) != $old_password){
                $this->_error(self::PASS_EXISTS);
                $result = false;
            }

        }else{
            $this->_error(self::PASS_EXISTS);
            $result = false;
        }
        return $result;
    }
}