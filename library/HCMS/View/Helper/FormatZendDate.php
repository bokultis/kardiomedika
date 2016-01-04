<?php
/**
 * Zend Date formatter
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author zeka
 *
 */
class HCMS_View_Helper_FormatZendDate {

    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Format zend date
     *
     * @param Zend_Date $date
     * @return string
     */
    public function formatZendDate($date) {
        return $date->toString("dd.MM.YYYY");
    }
}
?>
