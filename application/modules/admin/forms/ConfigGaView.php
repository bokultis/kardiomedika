<?php
/**
 * Config Widget  "View Id" add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author boris
 */
class Admin_Form_ConfigGaView extends HCMS_Form_Simple
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
            'view_id'  => array(
                'presence'      => 'required',
                'allowEmpty'    => true,
                'messages'      => array(
                    0 => 'Please specify View Id.'
                )
            )
            
        );


        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
