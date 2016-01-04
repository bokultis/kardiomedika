<?php
/**
 * Theme Custom view helper
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author Ilija Petkovic
 *
 */

class HCMS_View_Helper_ThemeCustom extends Zend_View_Helper_Abstract{
    
    
    public function themeCustom(){         
        $theme_settings = $this->view->application->get_theme_settings();
        $active_theme = $this->view->application->get_settings('theme');
        if(isset($theme_settings[$active_theme]))
            return $this->view->headStyle()->appendStyle($theme_settings[$active_theme]['css']); 
        else
            return '';
    }
}