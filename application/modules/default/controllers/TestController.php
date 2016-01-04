<?php
/**
 * CLI controller
 *
 * @package Contact
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_CliController extends HCLI_Controller_Action {

    /**
     *
     * @var Zend_Log
     */
    protected $_logger = null;

    public function echoAction() {
        /* @var $logger Zend_Log */
        $this->_logger = Zend_Registry::get('Zend_Log');
        $console = $this->getConsoleOptions(
                array('name|n=s' => 'Tell me your name')
        );
        $message = 'Hello ' . $console->getOption("name");
        echo $message, "\n";
        $this->_logger->log($message, Zend_Log::INFO);
        exit(0);
    }


    /**
     * Copy page content from files to DB
     *
     */
    public function pagetransferAction() {
        $console = $this->getConsoleOptions(array(
            'lang|l=s'      => 'Language',
            'theme|t=s'     => 'Theme',
            'format|f=s'    => 'New format'
        ));
        $lang = $console->getOption("lang")? $console->getOption("lang"): 'en';
        $theme = $console->getOption("theme")? $console->getOption("theme"): 'genesis';
        $newFormat  = $console->getOption("format")? $console->getOption("format"): 'php';
        
        $templatesRoot = APPLICATION_PATH . '/../themes/' . $theme . '/views/cms/page/static/';
        $criteria = array(
            'lang'          => $lang,
            'status'        => 'published'
        );        
        $pages = Cms_Model_PageMapper::getInstance()->fetchAll($criteria);
        /* @var $page Cms_Model_Page */
        foreach ($pages as $page) {
            if($page->get_format() != 'path'){
                continue;
            }
            echo $page->get_content() . "\n";
            $filePath = $templatesRoot . $page->get_content();
            if(!file_exists($filePath)){
                continue;
            }
            $page   ->set_content(file_get_contents($filePath))
                    ->set_format($newFormat);
            //print_r($page);
            Cms_Model_PageMapper::getInstance()->save($page, $lang);
            echo "\npage updated: " . $page->get_code() . " for lang $lang" . "\n";
        }
        
        exit(0);
    }
    
    /**
     * Copy page content from DB to files
     *
     */
    public function pagedb2filesAction() {
        $console = $this->getConsoleOptions(array(
            'lang|l=s'      => 'Language',
            'theme|t=s'     => 'Theme',
            'format|f=s'    => 'Old format'
        ));
        $lang = $console->getOption("lang")? $console->getOption("lang"): 'en';
        $theme = $console->getOption("theme")? $console->getOption("theme"): 'genesis';
        $oldFormat  = $console->getOption("format")? $console->getOption("format"): 'html';
        
        $templatesRoot = APPLICATION_PATH . '/../themes/' . $theme . '/views/cms/page/static/';
        $criteria = array(
            'lang'          => $lang,
            'status'        => 'published'
        );        
        if(!is_dir($templatesRoot . $lang)){
            mkdir($templatesRoot . $lang);
        }
        $pages = Cms_Model_PageMapper::getInstance()->fetchAll($criteria);
        /* @var $page Cms_Model_Page */
        foreach ($pages as $page) {
            if($page->get_format() != $oldFormat){
                continue;
            }
            echo $page->get_content() . "\n";
            $pagePath = $lang . '/' . $page->get_url_id() . ".phtml";
            $filePath = $templatesRoot . $pagePath;
            if(file_exists($filePath)){
                continue;
            }
            $content = $page->get_content();
            if(trim($content) == ''){
                $content = '<h1>' . $page->get_title() . '</h1>';
            }            
            file_put_contents($filePath, $content);
            $page   ->set_content($pagePath)
                    ->set_format('path');
            //print_r($page);
            Cms_Model_PageMapper::getInstance()->save($page, $lang);
            echo "\npage updated: " . $page->get_code() . " for lang $lang" . "\n";
        }
        
        exit(0);
    }    
}

