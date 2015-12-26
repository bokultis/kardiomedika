<?php

return array(
     'gender' => array(
        'name' => 'Gender',
        'template' => 'gender.phtml',
        'values'    => array(
            'male'   => 'Male',
            'female'   => 'Female'
        )         
    ),

    'name' => array(
        'name' => 'First Name',
        'filters' => array(new HCMS_Filter_Capitalize(), new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
    ),
    'input' => array(
        'filters' => array(new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
    ),
    'email' => array(
        'name' => 'Email',
        'filters' => array(new Zend_Filter_StringTrim(), new Zend_Filter_StripTags()),
        'validators' => array(
            new HCMS_Validate_EmailAddress(
                    array(
                        'allow' => Zend_Validate_Hostname::ALLOW_DNS,
                        'mx' => true,
                        'deep' => true
                    )
            )
        ),
        'messages' => array(
            'Please specify valid Email address'
        )
    ),
    'zip' => array(
        'name' => 'ZIP',
        'validators' => array(
            new Zend_Validate_GreaterThan(0),
            new Zend_Validate_Int()
        ),
        'messages' => array(
            'Zip code must be greater then 0.',
            'Zip code must be integer number.'
        )
    ),
    'country' => array(
        'name' => 'Country',
        'template' => 'country.phtml'
    ),
    'phone' => array(
        'name' => 'Phone',
        'validators' => array(
            new HCMS_Validate_Telephone(array("charlist" => array(" ", "/", "-")))
        ),
        'messages' => array(
            'Please specify valid Phone number.'
        )
    ),
    'message' => array(
        'name' => 'Message',
        'template' => 'textarea.phtml',
        'filters' => array(new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
    ),
    'select' => array(
        'name' => 'Values',
        'template' => 'select.phtml',
        'values' => array()
    ),
    'radio' => array(
        'name' => 'Values',
        'template' => 'radio.phtml',
        'values' => array()
    ),    
    'honeypot' => array(
        'name' => 'Honeypot',
        'template' => 'honeypot.phtml',
        'skip_email'    => true,
        'validators' => array(
            new Zend_Validate_Callback(function($value) {
                        return $value == null;
                    })
        )
    ),
    'captcha' => array(
        'name' => 'Captcha',
        'template' => 'captcha.phtml',
        'skip_email'    => true,
        'validators' => array(
            new HCMS_Captcha_Image(null)
        ),
        'params' => array(
            'name' => 'captcha',
            'font' => APPLICATION_PATH . '/fonts/font4.ttf',
            'wordLen' => 3,
            'timeout' => 300,
            'imgDir' => './captcha/',
            'imgUrl' => '/captcha/',
            'width' => 150,
            'height' => 40,
            'dotNoiseLevel' => 50,
            'lineNoiseLevel' => 2
        )
    ),
    'file' => array(
        'name' => 'File',
        'template' => 'file.phtml',
        'filters' => array(new Zend_Filter_StringTrim(), new Zend_Filter_StripTags())
    )
);