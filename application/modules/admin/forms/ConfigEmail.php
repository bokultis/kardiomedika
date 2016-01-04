<?php
/**
 * ConfigEmail add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author marko
 */
class Admin_Form_ConfigEmail extends HCMS_Form_Simple
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
            'from_email'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify from email.'
                )
            ),
            'from_name'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify from name.'
                )
            ),
            'transport'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify transport.'
                )
            )
            
        );


        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
