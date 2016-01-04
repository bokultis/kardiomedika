<?php
/**
 * ConfigEmail add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author boris
 */
class Admin_Form_ConfigOg extends HCMS_Form_Simple
{
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
        
        $filterRules = array();
        $validatorRules = array(
            'image' => array(
                'presence' => 'required',
                'allowEmpty' => true,
//                new Zend_Validate_File_Exists($paths['real']),
                new HCMS_Validate_File_Extension(array(
                    "dir"       => $paths['real'],
                    "extensions"=> "png,jpeg,jpg,gif"
                )),
                'messages' => array(
                    0 => 'Please specify an Image.'
                )
            ),
            'description'  => array(
                'allowEmpty'    => true
            )
            
        );


        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
