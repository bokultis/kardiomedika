<?php

return array(
    'email' => array(
        'sender'    => array(
            "transport" => "smtp",
            "parameters" => array(
                "server" => "mail.horisen.com",
                "auth" => "login",
                "username" => "fbapp@horisen.biz",
                "password" => "Fbh0r1sen*9",
                "port" => "587"
            )                   
        ),
        "from_email" => "fbapp@horisen.biz",
        "from_name" => "HORISEN CMS",
        "reply_email" => "info@horisen.com",
        "to_emails" => array(
            array(
                "name" => "Boris",
                "email" => "boris@horisen.com"

            )
        )                
    )  
);
