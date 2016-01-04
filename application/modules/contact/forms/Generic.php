<?php
/**
 * Generic form
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Form_Generic extends HCMS_Form_Simple
{    
    /**
     *
     * @var Zend_Captcha_Base
     */
    protected $_captcha = null;
    
    protected $validatorRules = array();
    protected $filterRules = array();        
    
    
    public function loadFromArray(array $fields){
        foreach ($fields as $fieldId => $fieldArr) {
            if(isset($fieldArr['type']) && $fieldArr['type'] == 'static'){
                continue;
            }
            $this->validatorRules[$fieldId] = isset($fieldArr['validators'])? $fieldArr['validators']: array();
            $this->filterRules[$fieldId] =  isset($fieldArr['filters'])? $fieldArr['filters']: array();
            $this->validatorRules[$fieldId]['messages'] = isset($fieldArr['messages'])? $fieldArr['messages']: array();
            //required
            if(isset($fieldArr['required']) && $fieldArr['required']){
                $this->validatorRules[$fieldId]['presence'] = 'required';
                $this->validatorRules[$fieldId][] = new Zend_Validate_NotEmpty();
                $this->validatorRules[$fieldId]['messages'][] = sprintf('Please specify %s.', $fieldArr['name']) ;
            }else{
                $this->validatorRules[$fieldId]['allowEmpty'] = true;
            }
            //field count
            if(isset($fieldArr['maxlength']) && is_int($fieldArr['maxlength'])){
                $this->validatorRules[$fieldId][] = new Zend_Validate_StringLength(array('max' => $fieldArr['maxlength']));
                $this->validatorRules[$fieldId]['messages'][] = sprintf('%s can not be longer than %d characters', $fieldArr['name'], $fieldArr['maxlength']);
            }           
        }
    }
    
    public static function getFieldsArr(array $types, array $fields){
        $result = array();
        foreach ($fields as $fieldId => $field) {
            if(isset($field['type'])){
                if(!isset($types[$field['type']])){
                    continue;
                }
                $type = $types[$field['type']];                
            }
            $field = array_merge($type, $field);
            $result[$fieldId] = $field;
        }
        return $result;
    }
    
    /**
     * Set deffault field values in $data if defined
     * @param array $fields
     * @param array $data
     */
    public static function setDefaultValues($fields, &$data){
        foreach ($fields as $fieldId => $field) {
            if(isset($field['default_value'])){
                $data[$fieldId] = $field['default_value'];
            }
        }
    }    
    
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null, array $fields = array(), Zend_Controller_Request_Abstract $request = null) {       
        $this->loadFromArray($fields);
        parent::__construct($this->filterRules,$this->validatorRules, $data, $options);
    }    
}