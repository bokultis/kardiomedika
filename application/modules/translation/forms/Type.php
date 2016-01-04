<?php
/**
 * Translation Type form
 *
 * @package Translation
 * @subpackage Form
 * @copyright Horisen
 * @author Marko
 */
class Translation_Form_Type extends HCMS_Form_Simple
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
            'name' => array(
                            'allowEmpty' => false,
                            new Zend_Validate_Db_NoRecordExists(
                                array(
//                                    'adapter' => Zend_Registry::get('translation_db'),
                                    'table' => $this->_prefix.'translate_type',
                                    'field' => 'name',
                                    'exclude' => array(
                                                        'field' => 'id',
                                                        'value' => $data['id']
                                                )
                                )
                            )
            )
          );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

