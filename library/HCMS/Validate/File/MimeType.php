<?php
/**
 * File mime type Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author milan
 */
class HCMS_Validate_File_MimeType extends Zend_Validate_File_MimeType
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
     * Mimetype to accept
     *
     * @param  string|array $mimetype MimeType
     * @param string real dir
     * @return void
     */
    public function __construct($mimetype,$dir = null)
    {
        parent::__construct($mimetype);

        if (isset($dir)) {
            $this->_dir = $dir;
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