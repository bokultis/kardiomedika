<?php

/**
 * CategoryData subform
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author Goran
 */
class Cms_Form_Sub_CategoryData extends HCMS_Form_Simple {

    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null) {
        /* @var $fileHelper HFB_File_Helper */
        $fileHelper = Zend_Registry::get("fileHelper");
        $paths = $fileHelper->getPath("");
        
        $validatorRules = array(
            'show_pic' => array(
                'presence' => 'required',
                'allowEmpty' => true,
            )
        );
        if(isset ($data['show_pic']) && $data['show_pic'] == 'yes') {
            $validatorRules['picture'] = array(
                'presence' => 'required',
                'allowEmpty' => false,
                new Zend_Validate_File_Exists($paths['real']),
                new HCMS_Validate_File_Extension(array(
                    "dir"       => $paths['real'],
                    "extensions"=> "png,jpeg,jpg,gif"
                )),
                'messages' => array(
                    0 => 'Please specify a File.'
                )

            );
        }
        
        parent::__construct(array(), $validatorRules, $data, $options);
    }

}

