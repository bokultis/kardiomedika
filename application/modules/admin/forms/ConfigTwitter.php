<?php
/**
 * ConfigTwitter add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author Boris
 */
class Admin_Form_ConfigTwitter extends HCMS_Form_Simple
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
            'user_id'  => array(
                'allowEmpty'    => true,
                'messages'      => array(
                    0 => 'Please specify User Id.'
                )
            ),
            'screen_name'  => array(
                'allowEmpty'    => true,
                'messages'      => array(
                    0 => 'Please specify User Screen Name.'
                )
            ),
            'count'  => array(
                'allowEmpty'    => true,
                'messages'      => array(
                    0 => 'Please specify Count of Tweets.'
                )
            )
            
        );


        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
