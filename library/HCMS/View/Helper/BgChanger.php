<?php
/**
 * View helper which sets background style
 *
 * @package Bgchanger
 * @subpackage View
 * @copyright Horisen
 * @author dzonz & Ilija
 *
 */
class HCMS_View_Helper_BgChanger extends Zend_View_Helper_Abstract {
    /**
     * Returns inline style for a css selector
     *
     */
    public function bgChanger ($cssSelector = 'body') {
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll(array('status'=>'active'));
        $active = false;
        foreach($modules as $module){
            $activeModules = $module->get_code();
            if($activeModules == "bgchanger"){
                $active = true;
            }
        }
        if(!$active){
            return '';
        }
        //$bgchanger = new 
        $activePage = null;
        if(!isset ($this->view->menuItems)){
            return '';
        }
        //Zend_Debug::dump($this->view->menuItems);
        
        foreach ($this->view->menuItems as $currMenu => $menuContainer) {
            
            
            $activeMenu = $this->view->navigation()->findActive($menuContainer);
            
            if(isset ($activeMenu) && isset ($activeMenu['page'])){
                /*@var $activePage Zend_Navigation_Page_Mvc */
                $activePage =  $activeMenu['page'];
                break;
            }
        }
       
        $html = '';
        if ($activePage != null) {
            $menuItemBg = new Bgchanger_Model_MenuItemBackground();
            $backgrounds = false;
        
        //  TODO: OPTIONAL MAPPER FOR CONTENT
        //             
        //  MAPPER FOR MENU
            if ($activePage->entity instanceof Cms_Model_MenuItem) {
                $i = 0;
            // LOOP THROUGH PARENTS
                while ($activePage && isset($activePage->entity) && $activePage->entity) {
                    $backgrounds = Bgchanger_Model_MenuItemBackgroundMapper::getInstance()->fetchAll(array(
                        'cms_menu_item_id' => $activePage->entity->get_id()
                    ));
                    
                    /*Bgchanger_Model_MenuItemBackgroundMapper::getInstance()->find(
                        $activePage->entity->get_id(), 
                        $menuItemBg
                    );*/
                    if ($backgrounds && count($backgrounds))
                        break;
                    
                    $activePage = $activePage->getParent();
                //  JUST A HACK IF SOMETHING GOES WRONG ASSUMING THERE WILL BE NO MORE THAN 10 LEVELS
                    $i++; if ($i > 10) break;
                }
            }
            
            $css = '';
            
            if ($backgrounds && count($backgrounds)) {
                $menuItemBg = $backgrounds[0];
                        
                $bacgrounds = array_reverse($menuItemBg->get_content());
                
                foreach ($bacgrounds as $key => $value) {

                   $elements = explode("_", $key);
                   $css .= "
                   @media (min-width: " . $elements[1] . "px) {
                    " . $cssSelector . "{
                        background-image: url('" . $value . " '); 
                        }

                   }"; 
                }
                

                $html = "                    
                        <style type='text/css'>
                            " . $css . "
                        </style>
                        ";
            }
        }
        return $html;
    }
}
