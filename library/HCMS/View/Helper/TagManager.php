<?php
/**
 * View helper which senders custom tags
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_TagManager extends Zend_View_Helper_Abstract {
    
    /**
     *
     * @var HCMS_Tags_TagManager 
     */
    protected $tagManager;
    
    /**
     * 
     * @return HCMS_Tags_TagManager
     */
    function getTagManager()
    {
        if(!isset($this->tagManager)){
            $this->tagManager = new HCMS_Tags_TagManager();
        }
        return $this->tagManager;
    }

        
    
    /**
     * Get custom Tags
     *
     * @param Application_Model_Application $app
     */
    public function tagManager ($position, $app = null) {
        if(!isset($app) && isset($this->view->application)){
            $app = $this->view->application;
        }
        if(!isset($app)){
            return '';
        }
        $settings = $app->get_settings('tags');
        if(!isset ($settings)){
            return '';
        }        
        
        return $this->getTagManager()->getTagsHtml($settings, $position);
    }
}