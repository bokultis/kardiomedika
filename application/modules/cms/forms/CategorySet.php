<?php
/**
 * Category add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author zeka
 */
class Cms_Form_CategorySet extends HCMS_Form_Simple
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
            'description' => array(
                 'allowEmpty'    => true
            ),
            'module'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Module.'
                )
            ),
            'page_type_id'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify Page Type.'
                )
            )
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}

