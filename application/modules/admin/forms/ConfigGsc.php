<?php
/**
 * ConfigEmail add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author boris
 */
class Admin_Form_ConfigGsc extends HCMS_Form_Simple
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
                'allowEmpty'    => true,
                'messages'      => array(
                    0 => 'Please specify Name.'
                )
            ),
            'cx'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify CX.'
                )
            ),
            'active'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            
            
            'title-color'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            
            'title-font-size'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            
            'snippet-color'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            
            'snippet-font-size'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            
            'visible-url-color'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            
            'visible-url-font-size'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify is Active.'
                )
            ),
            
            
            
        );


        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
