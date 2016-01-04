<?php

/**
 * Sessions controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_GoogleDashboardSessionsController extends HCMS_Controller_Action_Admin {

    public function widgetAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
        }

    }

}
?>