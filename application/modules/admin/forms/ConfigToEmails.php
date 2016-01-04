
<?php
/**
 * ConfigToEmails add/edit form
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author marko
 */
class  Admin_Form_ConfigToEmails extends HCMS_Form_Simple
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
            'name'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                'messages'      => array(
                    0 => 'Please specify name.'
                )
            ),
            'email'  => array(
                'presence'      => 'required',
                'allowEmpty'    => false,
                 new Zend_Validate_EmailAddress,
                'messages'      => array(
                    0 => 'Please specify email.'
                )
            )
        );

        parent::__construct($filterRules, $validatorRules, $data, $options);
    }
}
