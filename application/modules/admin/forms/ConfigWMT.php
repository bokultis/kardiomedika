<?php
/**
 * Config Webmaster tools meta tag add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author boris
 */
class Admin_Form_ConfigWMT extends HCMS_Form_Simple
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
            'wmt_active'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            'wmt_meta'  => array(
                'presence'      => 'required',
                'allowEmpty'    => true,
                new Admin_Form_Validator_Metatag(),
                'messages'      => array(
                    0 => 'Please specify propper Meta tag.'
                )
            )
            
        );


        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
