<?php
/**
 * Teaser  form
 *
 * @package Modules
 * @subpackage Teaser
 * @copyright Horisen
 * @author milan
 */
class Teaser_Form_Teaser extends HCMS_Form_Simple
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
            'box_code'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Box Code.'
                )
            ),
            'name' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Name.'
                )
            ),
            'menu_item_ids' => array(
                'allowEmpty'    => true
            ),
            'all_menu_items' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please Menu items'
                )
            ),
            'content' => array(
                'allowEmpty'    => true
            )            
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

