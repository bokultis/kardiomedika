<?php
/**
 * File extension Validator
 *
 * @package HCMS
 * @subpackage Validate
 * @copyright Horisen
 * @author milan
 */
class HCMS_Validate_File_GenericExtension extends Zend_Validate_File_Extension
{
    /**
     * Real dir
     *
     * @var string
     */
    protected $_dir = null;
    
    protected $_formId;
    protected $_fieldId;
    protected $_formParams = array();
    protected $_fields = array();
    protected $_globalSettings = array();
    

    
    protected function _loadParams()
    {      
        $this->_globalSettings = HCMS_Utils::loadThemeConfig('config.php', 'contact');
        $fieldTypes = HCMS_Utils::loadThemeConfig('types.php', 'contact');

        if(!isset($this->_globalSettings['forms'][$this->_formId])){            
            throw new Exception("Form not found");
        }
        
        $this->_formParams = $this->_globalSettings['forms'][$this->_formId];
        $this->_fields = Contact_Form_Generic::getFieldsArr($fieldTypes, $this->_formParams['fields']);
    }
    
    /**
     * Sets validator options
     *
     * @param  string|array|Zend_Config $options
     * @return void
     */
    public function __construct($options)
    {
        
        if(isset($options['form_id']) && $options['form_id'] != ''){
            $this->_formId = $options['form_id'];
        }
        if(isset($options['field_id']) && $options['field_id'] != ''){
            $this->_fieldId = $options['field_id'];
        }
        
        $extensions = array();
        parent::__construct($extensions);
        
        
        if(Zend_Registry::isRegistered('genericFileHelper')){            
            $fileHelper = Zend_Registry::get('genericFileHelper');
            $paths= $fileHelper->getTmpPath("");
            $fileRoot = $paths['real'];
        } else {
            $fileRoot = APPLICATION_PATH . '/../tmp/tmp_upload/1';
        }                
        
        $this->_dir = $fileRoot;
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
        $this->_loadParams();
        $extensions = $this->_fields[$this->_fieldId]['params']['extensions'];
        if(is_array($extensions) && count($extensions)){
            foreach ($extensions as $ext){
                $this->addExtension($ext);
            }
        }

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