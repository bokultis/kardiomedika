<?php
/**
 * Date validator
 *
 * @package Modules
 * @subpackage Consumer
 * @copyright Horisen
 * @author bane
 */
class Auth_Form_Validator_PasswordConfirmation extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'You need to confirm Password'
    );


    /**
     * data
     *
     * @var mixed
     */
    protected $_data;

    /**
     * Sets validator options
     *
     * @param  array $data
     * @return void
     */
    public function __construct($data){
        $this->_data = $data;        
    }

    public function isValid($value){
        
        if (isset($this->_data['id']) && $this->_data['id'] != ''){
            if(isset($this->_data['new_pass_confirm']) && $value == $this->_data['new_pass_confirm']){
                return true;
            }
        }else{
            if(isset($this->_data['pass_confirm']) && $value == $this->_data['pass_confirm']){
                return true;
            }
        }
        
        $this->_error(self::NOT_MATCH);
        return false;
    }

}