<?php
/**
 * Render selected on select box
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_FormSelected {

    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Render selected on select bo.
     *
     * @param array $values
     * @param string $field
     * @param string $compareValue
     * @param string $attribute selected|checked
     * @return string
     */
    public function formSelected($values, $field, $compareValue, $attribute = 'selected') {
        if(isset ($values[$field]) && $values[$field] == $compareValue){
            return $attribute . '="' . $attribute . '"';
        }
        else{
            return '';
        }
    }
}
?>
