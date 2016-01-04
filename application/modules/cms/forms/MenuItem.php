<?php
/**
 * Menu Item add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author marko
 */
class Cms_Form_MenuItem extends HCMS_Form_Simple
{
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null) {
        if($data['route'] == ''){
            $data['dialog_url'] = '';
        }
//        $null = new Zend_Db_Expr("NULL");
//        if(isset($data['route_uri']) && $data['route_uri'] == '')
//            $data['route_uri'] =  $null;
        $filterRules = array();
        $validatorRules = array(
            'id'  => array(
                'allowEmpty'    => true
            ),
            'name'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Menu Name.'
                )
            ),
            'menu' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Menu.'
                )
            ),
            'level'  => array(
                'allowEmpty'    => true
            ),
            'parent_id'  => array(
                'allowEmpty'    => true
            ),
            'page_id'  => array(
                'allowEmpty'    => true
            ),
            'page_id_new'  => array(
                'allowEmpty'=> ($data['dialog_url'] != '')?false:true,
                'presence'  =>($data['dialog_url'] != '')? 'required':false,
                new Zend_Validate_NotEmpty(),
//                'messages'      => array(
//                   Zend_Validate_NotEmpty::IS_EMPTY => 'For this destination module page is required.'
//                )
//                
                self::MESSAGES => array(
                    0 => 'For this destination module, page is required.'
                )
            ),
            'route'  => array(
                'allowEmpty'    => true
            ),
            'path'  => array(
                'allowEmpty'    => true
            ),
            'params'  => array(
                'allowEmpty'    => true
            ),
            'params_old'  => array(
                'allowEmpty'    => true
            ),
            'uri'  => array(
                'allowEmpty'    => true
            ),
            'ord_num'  => array(
                'allowEmpty'    => true
            ),
            'hidden'  => array(
                'allowEmpty'    => true
            ),
            'route_uri'  => array(
                'allowEmpty'    => true,
                (isset($data['page_id']) && $data['page_id'] != '')?
                new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'adapter' => Zend_Registry::get('db'),
                                    'table' => 'cms_route',
                                    'field' => 'uri',
                                    'exclude' => array(
                                                'field' => 'page_id',
                                                'value' => $data['page_id']
                                    )
                                )
                            ): 
                new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'adapter' => Zend_Registry::get('db'),
                                    'table' => 'cms_route',
                                    'field' => 'uri',
                                    'exclude' => array(
                                                'field' => 'path',
                                                'value' => (isset($data['path']))?$data['path']:''
                                    )
                                )
                        ),
                'messages'      => array(
                    0 => 'URL is already assigned to an item from the menu.'
                )
            ),
            'meta'  => array(
                'allowEmpty'    => true
            ),
            'target'  => array(
                'allowEmpty'    => true
            ),
            'dialog_url'  => array(
               'allowEmpty'    => true
            )
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

