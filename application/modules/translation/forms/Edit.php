<?php
/**
 * Translation Edit form
 *
 * @package Translation
 * @subpackage Form
 * @copyright Horisen
 * @author Ilija
 */
class Translation_Form_Edit extends HCMS_Form_Simple
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
        $filterRules = array();
        $validatorRules = array(
             
            'key' => array(
                            'presence'      => 'required',
                            'allowEmpty' => false,
                            
                            new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'table' => $this->_prefix.'translate',
                                    'field' => 'key',
                                    'exclude' => array(
                                                    'field' => 'id',
                                                    'value' => $data['id']
                                                )
                                )
                            )
            ),
            'section' => array(
                'presence'  => 'required',
                'allowEmpty' => false
            )
            
          );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

