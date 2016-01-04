<?php

/**
 * Admin front controller
 * 
 * Manage CMS pages inline from front-end
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_AdminFrontController extends HCMS_Controller_Action_Admin {
    
    const SESSION_NAMESPACE = 'admin_front';
    
    protected function sendResult($success, $message){
        return $this->_helper->json(array(
            'success'   => $success,
            'message'   => $message
        ));
    }
    
    /**
     * Ajax save page content
     * 
     * @return mixed
     */
    public function saveAction(){
        $pageId = $this->_getParam('id');
        $lang = $this->_getParam('lang');
        $content = $this->_getParam('content');
        
        if(!$pageId){
            return $this->sendResult(false, 'No page defined');
        }
        if(!$lang){
            return $this->sendResult(false, 'No lang defined');
        }
        if(!$content){
            return $this->sendResult(false, 'No content defined');
        }        

                   
        $page = new Cms_Model_Page();
        if(!Cms_Model_PageMapper::getInstance()->find($pageId, $page, $lang)){
            return $this->sendResult(false, 'Page not found');
        }
        $page->set_content($content);
        Cms_Model_PageMapper::getInstance()->save($page,$lang);
        return $this->sendResult(true, 'Content saved');
    }
    
    /**
     * Ajax enable/disable edit mode
     * @return mixed 
     */
    public function modeAction(){
        $enabled = $this->_getParam('enabled') == 1;
        $session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        $session->enabled = $enabled;
        return $this->sendResult(true, 'done');
    }
}