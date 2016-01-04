<?php
/**
 * File Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author milan
 */
class HCMS_Validate_File extends Zend_Validate_Abstract
{

     const ERR_FILE_UPLOAD = 'Error file upload';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
            self::ERR_FILE_UPLOAD => "'%value%' Error file upload"
     );

     /**
     * Options
     *
     * @var array
     */
    protected $_options;

    /**
     *
     * @var HFB_File_Helper
     */
    protected $_fileHelper = null;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'field' => 'form field name'
     *   'dir' => 'subdirectory for this file'
     *   'extensions' => array('jpg','png' ...)
     *   'mimetypes' => array('image/jpg', 'image/png')
     *
     * @param  array $options
     * @return void
     */
    public function __construct(HCMS_File_Helper $fileHelper,$options){
        $this->_options = $options;
        $this->_fileHelper = $fileHelper;
    }

    public function isValid($value, $context = null){
        $result = $this->_fileHelper->upload($this->_options);

        if($result === false){            
            $this->_error(self::ERR_FILE_UPLOAD);
            return false;
        }

        $value = (string) $result;
        $this->_setValue($value);       
    }

}