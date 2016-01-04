<?php
/**
 * Application add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author marko
 */
class Admin_Form_ConfigNotes extends HCMS_Form_Simple
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
            'notes'  => array(
                'allowEmpty'    => true
            )
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}