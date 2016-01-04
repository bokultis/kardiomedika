<?php

/**
 * Password reset form
 * @package Modules
 * @subpackage Auth
 * @copyright Horisen
 * @author Ilija
 */

class Auth_Form_Reset extends HCMS_Form_Simple{
    
    public function __construct(array $data = null, array $options = null){
        $passwordOpts = array(
            'requireAlpha' => true,
            'requireNumeric' => true,
            'requireCapital' => false,
            'minPasswordLength' => 8);

        $filterRules = array();
        $validatorRules = array(
            'id' => array(
                'allowEmpty'    => true
            ),
            'new_password'  => array(                
                'allowEmpty'    => false,                                
                new Auth_Form_Validator_PasswordStrong($passwordOpts),
                new Auth_Form_Validator_PasswordSpecialCharacters()                
            ),
            
            'new_password_confirm'  => array(                
                'allowEmpty'    => true,
                'validators' => array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'new_password'
                    )
                ),                
                new Auth_Form_Validator_PasswordStrong($passwordOpts),
                new Auth_Form_Validator_PasswordHistory($data),
                new Auth_Form_Validator_PasswordSpecialCharacters(),
                new Auth_Form_Validator_PasswordDuplicate($data)
                
            ),
        );
        
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}