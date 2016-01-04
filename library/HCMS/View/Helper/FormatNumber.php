<?php
/**
 * Number formatter
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author zeka
 *
 */
class HCMS_View_Helper_FormatNumber {

    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Format number
     *
     * @param float $number
     * @param string $type payment|credit
     * @return string
     */
    public function formatNumber($number,$type = 'payment') {
        switch($type){
            case "credit":
                return HCMS_Utils_Number::formatCreditAmount($number);
            case "payment":
                return HCMS_Utils_Number::formatPaymentAmount($number);
        }
        
    }
}
?>
