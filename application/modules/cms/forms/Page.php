<?php

/**
 * Page add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author milan
 */
class Cms_Form_Page extends HCMS_Form_Simple {

    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null, $lang = null) {
        $filterRules = array(
            'url_id' => HCMS_Filter_CharConvert::createSEOFilter($data, $options)
        );
        $validatorRules = array(
            'id' => array(
                'allowEmpty' => true
            ),
            'title' => array(
                'presence' => 'required',
                'allowEmpty' => false,
                'messages' => array(
                    0 => 'Please specify Page title.'
                )
            ),
            'code' => array(
                //'presence'      => 'required',
                'allowEmpty' => true,
                'messages' => array(
                    0 => 'Please specify Page code.'
                )
            ),
            'url_id' => array(
                'presence' => 'required',
                new Zend_Validate_NotEmpty(),
                new Cms_Form_Validator_UrlId($data, $lang),
                'messages' => array(
                    0 => 'Please specify URL ID.',
                    1 => 'The same URL Id already exists in some other page.'
                )
            ),
            'status' => array(
                'presence' => 'required',
                'allowEmpty' => false
            ),
            'type_id' => array(
                'presence' => 'required',
                'allowEmpty' => false
            ),
            'format' => array(
                'presence' => 'required',
                'allowEmpty' => false
            ),
            'teaser' => array(
                'allowEmpty' => true
            ),
            'content' => array(
                'allowEmpty' => true
            ),
            'meta' => array(
                'allowEmpty' => true
            ),
            'data' => array(
                'allowEmpty' => true
            )
        );
        parent::__construct($filterRules, $validatorRules, $data, $options);
    }

}

