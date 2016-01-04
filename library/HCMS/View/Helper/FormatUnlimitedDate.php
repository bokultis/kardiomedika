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
class HCMS_View_Helper_FormatUnlimitedDate {

    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }
    
    public function formatUnlimitedDate($date, $str_unlimited = '')
    {
        if (strpos($date,'3000-01-01') !== false) {
            return $str_unlimited;
        } else {
            return HCMS_Utils_Time::timeMysql2Local($date);
        }
    }
}
?>