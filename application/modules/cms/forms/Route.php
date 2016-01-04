<?php
/**
 * Route  add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author marko
 */
class Cms_Form_Route extends HCMS_Form_Simple
{
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null) {
        $filterRules = array();
        $validatorRules = array(
            'id'  => array(
                'allowEmpty'    => true
            ),
            'uri' => array(
                'allowEmpty' => true,
                new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'adapter' => Zend_Registry::get('db'),
                                    'table' => 'cms_route',
                                    'field' => 'uri',
                                    'exclude' => array(
                                                        'field' => 'page_id',
                                                        'value' => (isset($data['page_id']))? $data['page_id']:''
                                                )
                                )
                            ),
                'messages'      => array(
                    0 => 'URL is already assigned to an route.'
                )
            ),
            'name' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Name.'
                )
            ),
            'path' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Destination Module.'
                )
            ),
            'params' => array(
                'allowEmpty' => true
            ),
            'lang' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                new Zend_Validate_NotEmpty(),
                'messages'      => array(
                    0 => 'Please specify Language.'
                )
            ),
            'page_id' => array(
                'allowEmpty'=> (isset($data['dialog_url']) && $data['dialog_url'] != '')?false:true,
                'presence'  =>(isset($data['dialog_url']) && $data['dialog_url'] != '')? 'required':false,
                new Zend_Validate_NotEmpty(),
                'messages'      => array(
                    0 => 'For this destination module, page is required.'
                )
            )
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

