<?php
/**
 * Error messages Rendering
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_ErrorMessages {

    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Display Error Array as messages.
     *
     * @param array $fieldErrors
     * @param string $class
     * @param string $field
     * @return string
     */
    public function errorMessages($fieldErrors, $class = "error", $field = "", $justReturnClass = false) {
        if($field != ""){
            if(!isset ($fieldErrors[$field])){
                return;
            }
            $fieldErrors = $fieldErrors[$field];
        }
        if(isset ($fieldErrors) && count($fieldErrors)) {
            if($justReturnClass){
                return $class;
            }
            $result = "<ul class=\"$class\">";
            foreach ($fieldErrors as $error => $message) {
                //translate from here?
                //$result .= '<li>' . htmlspecialchars($this->_view->translate($message)) . '</li>';
                    $result .= '<li>' . htmlspecialchars($message) . '</li>';
                }
            $result .= '</ul>';
            return $result;
        }
    }
}
