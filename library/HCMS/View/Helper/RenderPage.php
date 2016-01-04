<?php
/**
 * Render page
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_RenderPage extends Zend_View_Helper_Abstract {
    
    /**
     * Get page string
     *
     * @return string
     */
    public function renderPage(Cms_Model_Page $page) {
        $pageFormat = $page->get_format();
        //render differently based on format
        switch($pageFormat) {
            case 'html':
                return $page->get_content();
                break;
            case 'php':
                return $this->_renderPageAsView($page);
                break;
            case 'path':
                return $this->view->render('page/static/' .$page->get_content());
                break;
        }        
    }
    
    /**
     * Stores content as view script in cache and renders it
     * 
     * @param Cms_Model_Page $page
     */
    protected function _renderPageAsView(Cms_Model_Page $page) {                
        $cacheId = HCMS_Controller_Action_Cms_Page::CMS_PAGE_CACHE_PRE . $page->get_id();
        $resources = Zend_Controller_Front::getInstance()->getParam("bootstrap")->getOption('resources');
        $cacheDir = $resources['cachemanager']['core']['backend']['options']['cache_dir'];
        $cacheFileName = 'zend_cache---' . $cacheId;
        // Create Zend_Cache_Core object
        $cache =  HCMS_Cache::getInstance()->getCoreCache();
        // If no cache available for this content page
        if(!$cache->test($cacheId)){
            $cache->save((string)$page->get_content(), $cacheId);
            //forcing raw string :(
            file_put_contents($cacheDir . "/" . $cacheFileName, $page->get_content());
        }        
        $this->view->addScriptPath($cacheDir);
        return $this->view->render($cacheFileName);
    }    

}
