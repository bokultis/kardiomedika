<?php
/**
 * Meta tag validator
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author boris
 */
class Admin_Form_Validator_Metatag extends Zend_Validate_Abstract
{
    const NOT_META = 'INV_REL_SPEC';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_META => 'Please use propper meta tag format.'
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

    public function isValid($value){
        $result = true;
        if(preg_match('/(<meta (.*)\/>)/i', $value) == 0){            
            $this->_error(self::NOT_META);
            $result = false;
        }
        
        return $result;
    }
}