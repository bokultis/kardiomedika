<?php
/**
 * To Emails Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author zeka
 */
class HCMS_Validate_ToEmails  extends Zend_Validate_Abstract
{
    const NAME_EMAIL_EMPTY = "You must give not empty value for 'name' and 'email'";
    const NAME_EMPTY =  "You must give not empty value for 'name'";
    const EMAIL_EMPTY ="You must give not empty value for 'email'";
    const EMAIL_WRONG = "You must give a valid 'email'";


    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NAME_EMAIL_EMPTY => "You must give not empty value for 'name' and 'email'",
        self::NAME_EMPTY => "You must give not empty value for 'name'",
        self::EMAIL_EMPTY => "You must give not empty value for 'email'",
        self::EMAIL_WRONG => "You must give a valid 'email'"
    );

    /**
     * data
     *
     * @var mixed
     */
    protected $_data;
    
    public function __construct($options){
        $this->setData($options);
    }

    public function isValid($value){
      if( $this->_data == null || $this->_data == "" ||  !is_array($this->_data) || count($this->_data) == 0 ) {
            $this->_error(self::NAME_EMAIL_EMPTY);
            return false;
        }

        if( !isset($this->_data['name']) || $this->_data['name'] == "" ){
            $this->_error(self::NAME_EMPTY);
            return false;
        }

        if( !isset($this->_data['email'])  || $this->_data['email'] == ""  ){
            $this->_error(self::EMAIL_EMPTY);
            return false;
        }
        
        if(!filter_var( $this->_data['email'], FILTER_VALIDATE_EMAIL )){
            $this->_error(self::EMAIL_WRONG);
            return false;
        }

        return true;
    }


    /**
     * Sets data
     *
     * @param  mixed $data
     * @return Horisen_Validate_Telephone Provides a fluent interface
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
