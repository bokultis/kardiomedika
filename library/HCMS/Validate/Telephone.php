<?php
/**
 * Telephone Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author marko
 */
class HCMS_Validate_Telephone extends Zend_Validate_Abstract
{

     const INVALID = 'This field is required';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
            self::INVALID => "'%value%' incorrect telephone number"
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
     *   'min' => scalar, minimum border
     *   'max' => scalar, maximum border
     *   'inclusive' => boolean, inclusive border values
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

        $value = trim((string) $value);
        if (isset ($this->_data['charlist'])) {
            $value = str_replace($this->_data['charlist'], '', $value);
        }


        if(preg_match("/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/", $value)){
            return true;
        }
        $this->_error(self::INVALID);
        return false;
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