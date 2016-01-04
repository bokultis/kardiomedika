
<?php
/**
 * ConfigUploads add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author marko
 */
class  Admin_Form_ConfigUploads extends HCMS_Form_Simple
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
            'extensions'  => array(
               'allowEmpty' => false,
                'NotEmpty',
                'messages'      => array(
                    0 => 'Please specify Extension.'
                )
            ),
            'mimetypes'  => array(
                'allowEmpty'    => false,
                'NotEmpty',
                'messages'      => array(
                    0 => 'Please specify Mimetype.'
                )
            ),
            'default_extensions'  => array(
                'allowEmpty'    => false,
                'NotEmpty',
                'messages'      => array(
                    0 => 'Please specify Extension.'
                )
            ),
            'default_mimetypes'  => array(
                'allowEmpty'    => false,
                'NotEmpty',
                'messages'      => array(
                    0 => 'Please specify Mimetype.'
                )
            )
            
        );
        
        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
