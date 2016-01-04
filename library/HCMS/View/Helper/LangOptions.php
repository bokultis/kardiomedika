<?php
/**
 * Render Lang options
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_LangOptions {

    /**
     *
     * @var Zend_View
     */
    private $_view;

    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Switch lang
     *
     * @return string
     */
    public function langOptions($languages,$defaultLang, $onlyFront = true) {
        $result = "<option value=''> </option>";
        foreach ($languages as $langCode => $lang) {
            if($onlyFront && !$lang['front_enabled']){
                continue;
            }
            $result .= '<option value="' . $this->_view->escape($langCode) . '" ';
            if($langCode == $defaultLang) {
                $result .= 'selected="selected"';
            }
            $result .= '>' . $this->_view->escape($lang['name']) . '</option>';
        }
        return $result;
    }
}
