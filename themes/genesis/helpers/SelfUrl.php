<?php
/**
 * Page url without hash tags
 *
 * @package Themes
 * @subpackage Helpers
 * @copyright Horisen
 * @author milan
 *
 */
class Theme_View_Helper_SelfUrl extends HCMS_View_Helper_RenderPage {
    
    protected static $resultUrl = null;

    /**
     * Get contact page url
     *
     * @return string
     */
    public function selfUrl() {
        if(!isset(self::$resultUrl)){
            self::$resultUrl = str_replace('#', '', preg_replace('/(?:#[\w-]+\s*)+$/', '', $_SERVER['REQUEST_URI']));
        }
        return self::$resultUrl;        
    }
}
