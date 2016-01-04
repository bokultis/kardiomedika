<?php
/**
 * Item add/edit form
 *
 * @package Modules
 * @subpackage Teaser
 * @copyright Horisen
 * @author milan
 */
class Teaser_Form_TeaserItem extends HCMS_Form_Simple
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
                    0 => 'Please specify Box code.'
                )
            ),
            'fallback' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Fallback.'
                )
            ),
            'start_dt'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Start Date.'
                )
            ),
            'end_dt'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify End Date.'
                )
            ),
            'title' => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Title.'
                )
            ),
            'content' => array(
                'allowEmpty'    => true
            ),
            'item_template' => array(
                'allowEmpty'    => true
            ),            
            'teaser_ids' => array(
                new Zend_Validate_Callback(function($value) use ($data){
                    return isset($data['teaser_ids']) && is_array($data['teaser_ids']) && count($data['teaser_ids']) > 0;
                }),
                'presence'      => 'required',
                'allowEmpty'    => true,
                'messages'      => array(
                    0 => 'Please select at least one container',
                    1 => 'Please specify item container.'
                )
            )            
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

