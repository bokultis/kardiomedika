<?php
/**
 * View helper to enable Single page websites in front
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_SinglePage extends Zend_View_Helper_Abstract {                
    
    /**
     * Enable inline wysiwyg editor in front
     * 
     * @param string $content
     * @param Cms_Model_Page $page 
     */
    public function singlePage() {
        return $this;
    }
    
    protected function hashIdFromUrl($url){
        return basename($url);
    }
    
    protected function sectionClass($params = array()){
        if(isset($params['sectionClass']) && $params['sectionClass'] != ''){
            return $params['sectionClass'];
        }
        return '';
    }

    public function fixMenuUrls($menuHtml, $options = array(), $compoundUrl = false){
        return preg_replace_callback('|href=\"(.+)\"|',
                function($matches) use ($compoundUrl){
                    if($compoundUrl){
                        return 'href="'.$matches[1].'#'.$this->hashIdFromUrl($matches[1]).'"';
                    }else{
                        return 'href="#' . $this->hashIdFromUrl($matches[1]) . '"';
                    }
                    
                }, $menuHtml);
    }
    
    protected function wrapSection($content, $navItem, array $options)
    {
        $result = isset($options['section_start'])? $options['section_start']: '<section class="anchorFix '. $this->sectionClass($navItem->get("params")) .'"';
        $result .= ' role="' . $this->hashIdFromUrl($navItem->getHref()) . '" id="' . $this->hashIdFromUrl($navItem->getHref()) . '"><div class="container">' . $content;
        $result .= isset($options['section_end'])? $options['section_end']: '</div></section>';
        return $result;
    }
    
    /**
     * Render sections
     * 
     * @param Zend_Navigation_Container $navigation
     * @params array $options
     * @return string
     */
    public function renderSections(Zend_Navigation_Container $navigation, $options = array())
    {
        $html = '';
        $iterator = new RecursiveIteratorIterator($navigation, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $navItem) {
            /* @var $menuItem Cms_Model_MenuItem */
            $menuItem = $navItem->entity;
            if($menuItem->get_page_id() > 0){        
                $html .= $this->wrapSection($this->view->contentBlock($menuItem->get_page_id(), null, 'id'), $navItem, $options);
            } elseif($menuItem->get_path()){
                $path = explode('/', $menuItem->get_path());
                $params = $menuItem->get_params();
                $params['lang'] = CURR_LANG;
                $html .= $this->wrapSection($this->view->action($path[2], $path[1], $path[0], $params), $navItem, $options);
            }
        }
        
        return $html;
    }
    
    /**
     * get menu item
     * 
     * @param Zend_Navigation_Container $navigation
     * @params array $options
     * @return string
     */
    public function getSelectedMenuItem(Zend_Navigation_Container $navigation)
    {
        $navigation->rewind();
        return $navigation->current();
    }    

}
