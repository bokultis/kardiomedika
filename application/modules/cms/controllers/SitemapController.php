<?php

class Cms_SitemapController extends HCMS_Controller_Action_Cms
{

    /**
     *
     * @var Application_Model_Module
     */
    protected $_module = null;

    public function init(){
        parent::init();
        $this->_module = new Application_Model_Module();
        if(Application_Model_ModuleMapper::getInstance()->findByCode($this->getRequest()->getModuleName(), $this->_module)){
            $this->view->sitemapSettings = $this->_module->get_settings('sitemap');
        }
    }

    public function indexAction()
    {

    }

    public function xmlAction(){
        $this->_helper->layout->disableLayout();
        $this->getResponse()->setHeader('Content-type', 'text/xml');
    }
}