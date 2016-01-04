<?php
/**
 * Menu  add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author marko
 */
class Cms_Form_Menu extends HCMS_Form_Simple
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
            'code'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Menu Code.'
                )
            ),
            'name' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Menu Name.'
                )
            )
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

