<?php
/**
 * File extension Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author milan
 */
class HCMS_Validate_File_Extension extends Zend_Validate_File_Extension
{
    /**
     * Real dir
     *
     * @var string
     */
    protected $_dir = null;

    /**
     * Sets validator options
     *
     * @param  string|array|Zend_Config $options
     * @return void
     */
    public function __construct($options)
    {
        parent::__construct($options);

        if (isset($options['dir'])) {
            $this->_dir = $options['dir'];
        }
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the imagesize of $value is at least min and
     * not bigger than max
     *
     * @param  string $value Real file to check for image size
     * @param  array  $file  File data from Zend_File_Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        if ($file === null) {
            $file = array(
                'type' => null,
                'name' => $value
            );
        }
        if(isset ($this->_dir)){
            $value = $this->_dir . "/" . $value;
        }
        return parent::isValid($value, $file);
    }

}