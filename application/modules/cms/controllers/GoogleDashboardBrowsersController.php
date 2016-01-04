<?php

/**
 * Sessions controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_GoogleDashboardBrowsersController extends HCMS_Controller_Action_Admin {

    public function widgetAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
        }

    }

}
?>