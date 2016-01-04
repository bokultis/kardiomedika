<?php
/**
 * View helper - renders whole box with all items
 *
 * @package Teaser
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class Teaser_View_Helper_RenderTeaser2 extends Zend_View_Helper_Abstract {
    
    /**
     *
     * @var Zend_View
     */
    public static $renderView;
    
    /**
     * Choose proper teaser
     * 
     * @param array $teasers
     * @return Teaser_Model_Teaser 
     */
    public function getBestTeaser(array $teasers){
        if(count($teasers) == 1){
            return $teasers[0];
        }
        /*@var $teaser Teaser_Model_Teaser */
        foreach ($teasers as $teaser) {
            //the first which is not generic for all items is the best
            if($teaser->get_all_menu_items() != 'yes'){
                return $teaser;
            }
        }
        return $teasers[0];
    }
    
    public function renderTeaser2($code, $menuItemId = null, $lang = null, $params = array()) {
        if(!isset($lang)){
            $lang = CURR_LANG;
        }
        $teaserMapper = Teaser_Model_TeaserMapper::getInstance();
        //preview mode        
        if(isset($params['preview_teaser_id'])){
            //fetch all with code
            $teasers = $teaserMapper->fetchWithItems(array(
                'lang'              => $lang,
                'active'            => true,
                'box_code'          => $code,
                'preview_teaser_id' => $params['preview_teaser_id'],
                'preview_dt'        => $params['preview_dt']
            ));
        }
        //get active teasers
        elseif(isset($menuItemId)){            
            //try to speed up if we know the menu item, since we're caching them per page render            
            $teasers = $teaserMapper->getActiveTeasers($lang, $menuItemId, $code);
        }
        else{            
            //fetch all with code
            $teasers = $teaserMapper->fetchWithItems(array(
                'lang'      => $lang,
                'active'    => true,
                'box_code'  => $code
            ));
        }
        //no teasers found
        if(count($teasers) == 0){
            return;
        }
        //get first teaser
        /* @ var $teaser Teaser_Model_Teaser */
        $teaser = $this->getBestTeaser($teasers);
        $box = $teaserMapper->getBox($teaser->get_box_code());
        //get view        
        $view = $this->getView();
        $view->assign(array(
            'teaser'    => $teaser,
            'items'     => $this->_processFeedbackItems($teaser->get_items()),
            'box'       => $box
        ));
        return $view->render($box['template']);        
        
    }
    
    /**
     * Get view
     * 
     * @return Zend_View 
     */
    protected function getView(){
        if(!isset(self::$renderView)){
            //init view        
            self::$renderView = new Zend_View();
            self::$renderView->setScriptPath(APPLICATION_PATH . "/modules/teaser/views/scripts/templates/");
            self::$renderView->addHelperPath(APPLICATION_PATH . "/modules/teaser/views/helpers/", "Teaser_View_Helper");
            $mvcView = Zend_Layout::getMvcInstance()->getView();
            if(isset($mvcView->theme)){
                self::$renderView->addScriptPath(APPLICATION_PATH . '/../themes/' . $mvcView->theme . '/views/teaser/templates/');
            }
        }        
        return self::$renderView;
    }
    
    /**
     * Get no fallback items, or if no others then fallback
     * @param array $items
     * @return array
     */
    protected function _processFeedbackItems($items){
        $processedItems = array();
        /* @ var $item Teaser_Model_Item */
        foreach ($items as $item) {
            if($item->get_fallback() != 'yes'){
                $processedItems[] = $item;
            }
        }
        if(count($processedItems)){
            return $processedItems;
        }
        return $items;
    }
}