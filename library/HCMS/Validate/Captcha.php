<?php
/**
 * Captcha Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author marko
 */
class HCMS_Validate_Captcha extends Zend_Validate_Abstract
{

    /**
     * data
     *
     * @var mixed
     */
    protected $_data;

    /**
     *
     * @var Zend_Captcha_Base
     */
    protected $_captcha = null;
    
    /**
     * Sets validator options
     *
     * @param  Zend_Captcha_Base $captcha\
     * @param array $data
     * @return void
     */
    public function __construct($captcha, $data = array()){
        $this->_captcha = $captcha;
        $this->_data = $data;
    }

    public function isValid($value, $context = null){        
        $result = $this->_captcha->isValid($this->_data);
        if($result === false){
            $this->_errors   = $this->_captcha->getErrors();
            $this->_messages = $this->_captcha->getMessages();
        }
        return $result;
    }
}
