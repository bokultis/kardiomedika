<?php
/**
 * Test CLI controller
 *
 * @package Contact
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_TestcliController extends HCLI_Controller_Action {

    /**
     *
     * @var Zend_Log
     */
    protected $_logger = null;

    public function importAction() {
        /* @var $logger Zend_Log */
        $this->_logger = Zend_Registry::get('Zend_Log');
        $console = $this->getConsoleOptions(
                array('name|n=s' => 'Tell me your name')
        );        
        //$message = 'Hello ' . $console->getOption("name");
        $importer = new Cms_Model_SiteMapImporter();
        $importer->importXls('/work/web-apps/cms2/genesis/docs/specs/sitemap-template.xlsx');
    }
}

