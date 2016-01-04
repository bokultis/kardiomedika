<?php
/**
 * View helper for rendering forms
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_FieldHelper extends Zend_View_Helper_Abstract {
    protected $settings;
    protected $fieldName;
    
    public function FieldHelper($settings, $fieldName) {
        $this->settings = $settings;
        $this->fieldName = $fieldName;
        return $this;
    }
    
    public function isShown(){        
        $key = 'show_' . $this->fieldName . '_form_field';
        //echo $key;
        //Zend_Debug::dump($this->settings);
        return $this->isRequired() || (isset($this->settings[$key]) && $this->settings[$key] == 'yes');
    }
    
    public function isRequired(){
        $key = 'show_' . $this->fieldName . '_form_field_required';
        return isset($this->settings[$key]) && $this->settings[$key] == 'yes';
    }    
    
    public function printRequired(){
        if($this->isRequired()){
            echo '*';
        }
    }
    
    public function printMaxLength(){
        $key = 'show_' . $this->fieldName . '_form_field_count';
        $keyCount = $this->fieldName . '_form_field_count';
        //echo $key;
        //Zend_Debug::dump($this->settings);
        if(isset($this->settings[$key]) && $this->settings[$key] == 'yes' && isset($this->settings[$keyCount])){
            echo ' maxlength="' . $this->settings[$keyCount] . '"';
        }        
    }
}