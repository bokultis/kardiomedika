<?php
/**
 * Print value in input box
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_FormInputValue {

    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Print value in input box
     *
     * @param array $dataArr
     * @param string $field
     * @return string
     */
    public function formInputValue($dataArr, $field) {  
        if(isset ($dataArr) && isset ($dataArr[$field])){
            return $this->_view->escape($dataArr[$field]);
        }
        else{
            return '';
        }
    }
}
