<?php
/**
 * EMail Transport Factory
 *
 * @package HCMS
 * @subpackage Email
 * @copyright Horisen
 * @author milan
 */
class HCMS_Email_TransportFactory {

    /**
     * Get create factory
     * @param array $emailSettings
     * @return Zend_Mail_Transport_Smtp
     */
    public static function createFactory(array $emailSettings) {
        if($emailSettings['transport'] == "smtp") {
            return  new Zend_Mail_Transport_Smtp($emailSettings['parameters']['server'], $emailSettings['parameters']);
        }elseif($emailSettings['transport'] == "sendmail") {
            return new Zend_Mail_Transport_Sendmail();
        }
        throw new Zend_Exception("Wrong transport name");
    }
}
