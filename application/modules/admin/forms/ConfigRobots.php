<?php
/**
 * Config web robots
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author boris
 */
class Admin_Form_ConfigRobots extends HCMS_Form_Simple
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
            'robots_active'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            )
            
        );


        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
