<?php

class Cms_RedirectController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $url = $this->_getParam('url');
        if(!isset ($url)){
            throw new Zend_Controller_Action_Exception("Redirect page not found", 404);
        }
        $this->_redirect(urldecode($url));
        die();
    }
}