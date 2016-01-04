<?php
/**
 * Application add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author marko
 */
class Admin_Form_ConfigGeneral extends HCMS_Form_Simple
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
            'name'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Name.'
                )
            ),
            'status' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify status.'
                )
            ),
            'status_dt'  => array(
                'allowEmpty'    => true
            )
            
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}