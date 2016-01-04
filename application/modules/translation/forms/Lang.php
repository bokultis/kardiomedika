<?php
/**
 * Translation Lang form
 *
 * @package Translation
 * @subpackage Form
 * @copyright Horisen
 * @author Marko
 */
class Translation_Form_Lang extends HCMS_Form_Simple
{   
    protected $_prefix = "";
    /**
    * Construct Translation_Form_Type
    *
    * @param array $data
    * @param array $options
    */
    public function __construct(array $data = null, array $options = null)
    {   
        $tr = Zend_Registry::get('Zend_Translate');
         if(Zend_Registry::isRegistered('prefix_table')){
            $this->_prefix = Zend_Registry::get('prefix_table');
        }
        $filterRules = array(
            'name' => array(
                            new Zend_Filter_StringTrim(),
                            //new Zend_Filter_StringToLower()
            ),
            'code' => array(
                            new Zend_Filter_StringTrim(),
                            new Zend_Filter_StringToLower()
            )
        );
        $validatorRules = array(
             'id' => array(
                            'allowEmpty' => true,
                            new Zend_Validate_Digits(),
                            'messages' => array(
                                0 => "Id "."'$data[id]'".$tr->_(" contains characters which are not digits; but only digits are allowed ")
                            )
            ),
            'code' => array(
                            'presence'      => 'required',
                            'allowEmpty' => false,
                            new Zend_Validate_Db_NoRecordExists(
                                array(
//                                    'adapter' => Zend_Registry::get('translation_db'),
                                    'table' => $this->_prefix.'translate_language',
                                    'field' => 'code',
                                    'exclude' => array(
                                                        'field' => 'id',
                                                        'value' => $data['id']
                                                )
                                )
                            )
            ),
            'name' => array(
                            'allowEmpty' => false,
                            new Zend_Validate_Db_NoRecordExists(
                                array(
//                                    'adapter' => Zend_Registry::get('translation_db'),
                                    'table' => $this->_prefix.'translate_language',
                                    'field' => 'name',
                                    'exclude' => array(
                                                        'field' => 'id',
                                                        'value' => $data['id']
                                                )
                                )
                            )
            ),
            'default' => array(
                'presence'      => 'required',
                'allowEmpty' => false,
                'messages' => array(
                    0 => 'Please specify is default.'
                )
            ),
            'front_enabled' => array(
                'presence'      => 'required',
                'allowEmpty' => false,
                'messages' => array(
                    0 => 'Please specify is front enabled.'
                )
            )
          );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

