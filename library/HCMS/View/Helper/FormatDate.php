<?php
/**
 * Date formatter
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author zeka
 *
 */
class HCMS_View_Helper_FormatDate {

    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Format mysql date
     *
     * @param string $date
     * @return string
     */
    public function formatDate($date) {
        return HCMS_Utils_Time::timeMysql2Local($date);
    }
}
?>
