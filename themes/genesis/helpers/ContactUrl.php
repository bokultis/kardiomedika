<?php
/**
 * Contact url view helper
 *
 * @package Themes
 * @subpackage Helpers
 * @copyright Horisen
 * @author milan
 *
 */
class Theme_View_Helper_ContactUrl extends Zend_View_Helper_Abstract {

    /**
     * Get contact page url
     *
     * @return string
     */
    public function contactUrl() {
        return $this->view->url(array(
            'module' => 'contact',
            'controller' => 'generic',
            'action' => 'index',
            'form_id' => 'contact'
        ), 'cms');
    }
}
