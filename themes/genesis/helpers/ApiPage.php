<?php
/**
 * Custom api page renderer
 *
 * @package Themes
 * @subpackage Helpers
 * @copyright Horisen
 * @author milan
 *
 */
class Theme_View_Helper_ApiPage extends HCMS_View_Helper_RenderPage {

    /**
     * Get contact page url
     *
     * @return string
     */
    public function apiPage(Cms_Model_Page $page) {
        return $this->view->partial('templates/api_page.phtml',array(
            'pageContent'   => $this->renderPage($page)
        ));
    }
}
