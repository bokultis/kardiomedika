<?php

return array(
    'forms' => array(
        'contact' => array(
            'template'  => null,
            'email' => array(
                "confirmation_email" => "yes"
            ),
            'db' => array(
                'save'  => true
            ),
            'fields'    => array(
                 'gender'     => array(
                    'type'      => 'gender',
                    'name'      => 'Gender',
                    'required'  => true,
                    'values'    => array(
                        'male'   => 'Mr.',
                        'female'   => 'Mrs.'
                    ),
                    'css_class' => 'form-group error-wrapper-gender'
                ),

                'first_name'    => array(
                    'type'      => 'name',
                    'name'      => 'First name',
                    'placeholder'=> 'Your first name',
                    'required'  => true,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row'
                ),
                'last_name'     => array(
                    'type'      => 'name',
                    'name'      => 'Last name',
                    'placeholder'=> 'Your last name',
                    'required'  => true,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row right-half'
                ), 

                'company'     => array(
                    'type'      => 'name',
                    'name'      => 'Company',
                    'placeholder'=> 'Your company name',
                    'required'  => true,
                    'maxlength' => 50,
                    'css_class' => 'form-group'
                ),                 

                'email'         => array(
                    'name'      => 'Email',
                    'placeholder'=> 'e.g. your.name@example.com',
                    'type'      => 'email',
                    'required'  => true,
                    'maxlength' => 255,
                    'css_class' => 'form-group'
                ),
                'phone'         => array(
                    'type'      => 'phone',
                    'name'      => 'Phone',
                    'placeholder'=> 'e.g. +41 21 1234567',                    
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row'
                ),
                'mobile'         => array(
                    'type'      => 'phone',
                    'name'      => 'Mobile',
                    'placeholder'=> 'e.g. +41 78 1234567',                    
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row right-half'
                ),
                'street'    => array(
                    'type'      => 'input',
                    'name'      => 'Street',
                    'placeholder'=> 'Street',
                    'maxlength' => 50,
                    'css_class' => 'form-group'
                ),
                'zip'           => array(
                    'type'      => 'zip',
                    'name'      => 'Zip',              
                    'placeholder'=> 'e.g. 9400',
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row field-new-line'
                ),
                'city'          => array(
                    'type'      => 'name',
                    'name'      => 'City',                    
                    'placeholder'=> 'e.g. Rorschach',
                    'required'  => false,
                    'maxlength' => 50,
                    'css_class' => 'form-group half-row right-half'
                ),
                'country'       => array(
                    'type'      => 'country',
                    'name'      => 'Country',
                    'placeholder'=> 'Choose your country',                    
                    'params'    => array(
                        'ip_country_detection' => true,
                        'selected_only' => false,
                        'selected_countries' => array('CH'),                        
                    ),
                    'css_class' => 'form-group'
                ),                
                'message'       => array(
                    'type'      => 'message',
                    'name'      => 'Message',                    
                    'placeholder'=> 'Your message',
                    'required'  => true,
                    'maxlength' => 500,
                    'css_class' => 'form-group messageBox'
                ),
                'honepot'       => array(
                    'type'      => 'honeypot'
                ),
//               'fileupload'       => array(
//                    'type'      => 'file',
//                    'name'      => 'File upload',
//                    'placeholder'=> 'Upload file',   
//                    'required'  => true,
//                    'params'    => array(
//                        'maxsize'   => 2048576,
//                        'dir' => 'contact_uploads',
//                        'extensions' => array('pjpeg', 'jpeg', 'jpg', 'png', 'x-png', 'gif', 'pdf'),
//                        'mimetypes' => array('image/pjpeg', 'image/jpeg', 'image/jpg', 'image/png', 'image/x-png', 'image/gif', 'application/pdf')
//                    ),
//                   'validators' => array(
//                        new HCMS_Validate_File_GenericExtension(
//                                array(
//                                    'form_id' => 'contact',
//                                    'field_id' => 'fileupload'
//                                 )),
//                       new HCMS_Validate_File_GenericMimeType(
//                               array(
//                                    'form_id' => 'contact',
//                                    'field_id' => 'fileupload'
//                                 )),
//                        'messages' => array(
//                            0 => 'Please specify valid File.'
//                        )
//                    ),
//                    'css_class' => 'form-group'
//                ),

                               
            )
        )
    )   
);
