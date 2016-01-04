<?php
/**
 * Category add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author zeka
 */
class Cms_Form_Category extends HCMS_Form_Simple
{
    /**
     *
     * @var Cms_Form_Sub_CategoryData
     */
    protected $_dataSubform = null;
    
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null) {
        $filterRules = array(
            'url_id' => HCMS_Filter_CharConvert::createSEOFilter($data, $options)
        );
//        $subData = isset ($data['data'])?$data['data']:array();
//        $this->_dataSubform = new Cms_Form_Sub_CategoryData($subData, $options);
        $filterRules = array();
        $validatorRules = array(
            'id'  => array(
                'allowEmpty'    => true
            ),
            'url_id' => array(
                'presence' => 'required',
                new Zend_Validate_NotEmpty(),
                //new Cms_Form_Validator_UrlId($data, $lang),
                'messages' => array(
                    0 => 'Please specify URL ID.'
                    //1 => 'The same URL Id already exists in some other page.'
                )
            ),
            'set_id'  => array(
                'allowEmpty'    => false,
            ),
            'name'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Name.'
                )
            ),
            'data' => array(
                'allowEmpty'    => true
            ),
            'description' => array(
                'allowEmpty'    => true
            ),
            'parent_id'  => array(
                'allowEmpty'    => true
            ),
            'meta' => array(
                'allowEmpty' => true
            )
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);    
    }
//    
//    /**
//     * Is main form and subforms valid
//     *
//     * @return boolean
//     */
//    public function isValid($fieldName = null)
//    {
//        return parent::isValid() && $this->_dataSubform->isValid();
//    }
//
//    /**
//     * Get error messages for main and subforms
//     *
//     * @return array
//     */
//    public function getMessages()
//    {
//
//        $messages = parent::getMessages();
//
//        $dataMessages = $this->_dataSubform->getMessages();
//        if(count($dataMessages)){
//            $messages['data'] = $dataMessages;
//        }
//        return $messages;
//    }
//
//    /**
//     * Get unescaped valid form values for both main and subforms
//     *
//     * @return mixed
//     */
//    public function getValues(){
//        $values = parent::getValues();
//
//        $dataValues = $this->_dataSubform->getValues();
//        if(count($dataValues)){
//            $values['data'] = $dataValues;
//        }
//        return $values;
//    }
}

