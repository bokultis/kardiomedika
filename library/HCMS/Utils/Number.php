<?php
/**
 * Utility number class.
 *
 * @package HCMS
 * @subpackage Utils
 * @copyright Horisen
 * @author zeka
 */
class HCMS_Utils_Number {
    /**
     * Format number for a money amount
     *
     * @param float $amount
     * @return string
     */
    static public function formatPaymentAmount($amount) {
        return number_format((float)$amount, 2, '.', "'");
    }

    /**
     * Format number for a credit amount
     *
     * @param float $amount
     * @return string
     */
    static public function formatCreditAmount($amount) {
        return number_format((float)$amount, 4, '.', "'");
    }


}
