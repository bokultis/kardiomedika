<?php
/**
 * Password Confirmation Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author marko
 */
class HCMS_Validate_PasswordConfirmation  extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';
    const STRING_EMPTY = '';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match',
        self::STRING_EMPTY => "Please provide a password"
    );

    /**
     * data
     *
     * @var mixed
     */
    protected $_data;
    
    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'pass_confirm' => password confirmation
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options){
        $this->setData($options);
    }

    public function isValid($value, $context = null){

        $value = (string) $value;
        $this->_setValue($value);
        if (is_array($this->_data)){
            if (isset($this->_data['pass_confirm']) && ($value == $this->_data['pass_confirm'])){
                return true;
            }
        } elseif (is_string($this->_data) && ($value == $this->_data)) {
            return true;
        }
        $data = $this->getData();
        // print_r($data);
        $this->_error(self::NOT_MATCH);
        return false;
    }

    /**
     * Sets data
     *
     * @param  mixed $data
     * @return HCMS_Validate_PasswordConfirmation Provides a fluent interface
     */
    public function setData($data){
        $this->_data = $data;
        return $this;
    }

    /**
     * Returns data
     *
     * @return mixed
     */
    public function getData(){
        return $this->_data;
    }


}
