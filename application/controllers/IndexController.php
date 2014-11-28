<?php

class IndexController extends HCMS_Controller_Action_Cms
{
    public function indexAction()
    {
        if($this->getRequest()->getParam('module') != 'default'){
            throw new Zend_Controller_Action_Exception($this->translate("Page not found"),404);
        }
    }           
}