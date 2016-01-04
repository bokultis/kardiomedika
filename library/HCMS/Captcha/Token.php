<?php
/**
 * Custom captcha token code
 *
 * @package    HCMS
 * @subpackage Captcha
 * @copyright  Horisen
 */
class HCMS_Captcha_Token extends Zend_Validate_Abstract {
    
    const ERR_TOKEN_MATCH = 'notMatch';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERR_TOKEN_MATCH => 'Token does not match',
    );    
    
    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_session = null;
    
    /**
     * Validate the word
     *
     * @see    Zend_Validate_Interface::isValid()
     * @param  mixed      $value
     * @param  array|null $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $session = $this->getSession();
        //echo "$session->tokenId == $value;"; die();
        $result = $session->tokenId == $value;
        if(!$result){
            $this->_error(self::ERR_TOKEN_MATCH);
        }
        return $result;
    }
    
    /**
     * Get session
     * 
     * @return Zend_Session_Namespace 
     */
    protected function getSession(){
        if(!isset($this->_session)){
            $this->_session = new Zend_Session_Namespace('captcha_token');
        }
        return $this->_session;
    }
    
    public function generateNewToken(){
        $tokenId = md5(uniqid());
        $session = $this->getSession();
        $session->tokenId = $tokenId;
        return $tokenId;        
    }             
}

