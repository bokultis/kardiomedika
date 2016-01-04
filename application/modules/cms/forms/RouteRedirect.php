<?php
/**
 * Route  add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author marko
 */
class Cms_Form_RouteRedirect extends HCMS_Form_Simple
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
                'allowEmpty' => false,
                 'presence'      => 'required',
                new Zend_Validate_Db_NoRecordExists(
                                array(
                                    'adapter' => Zend_Registry::get('db'),
                                    'table' => 'cms_route',
                                    'field' => 'uri',                                  
                                    'exclude' => array(
                                                        'field' => 'id',
                                                        'value' => (isset($data['id']))? $data['id']:''
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
            'params' => array(
                 'presence'      => 'required',
                 'allowEmpty'    => false,
                 new Zend_Validate_Callback(array('Zend_Uri', 'check')),
                 'messages'      => array(
                    0 => 'Please specify valid URL which starts with http:// or https:// .'
                )
            ),
            'lang' => array(
                'presence'      => 'required',
                'allowEmpty'    => true,
                //new Zend_Validate_NotEmpty(),
                'messages'      => array(
                    0 => 'Please specify Language.'
                )
            ),           
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

