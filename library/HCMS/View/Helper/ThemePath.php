<?php
/**
 * Theme Path view helper - prefixes theme path
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_ThemePath extends Zend_View_Helper_Abstract {

    /**
     * Theme Path view helper - prefixes theme path
     *
     * @param string $path Path without theme
     * @return string
     */
    public function themePath($path) {
        if(!isset ($this->view) || !isset ($this->view->theme)){
            return $path;
        }
        return '/themes/' . $this->view->theme . '/' . $path;
    }
}
