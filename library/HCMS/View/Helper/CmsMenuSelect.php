<?php
/**
 * CMS menu render select box
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_CmsMenuSelect extends Zend_View_Helper_Abstract {

    /**
     * CMS menu render select box
     *
     * @param int $pageId page id
     * @return string
     */
    public function cmsMenuSelect(array $params = array()) {
        //menu items
        $menuItems = array();
        $this->_populateMenuItems($menuItems);
        $selectId = isset($params['id']) ? $params['id']: 'menu_item_id';
        $selectName = isset($params['name']) ? $params['name']: 'menu_item_id';
        $defaultOption = isset($params['default_text'])? $params['default_text']: '';
        $selectedValues = array();
        if(isset($params['selected'])){
            if(is_array($params['selected'])){
                $selectedValues = $params['selected'];
            }
            else{
                $selectedValues = array($params['selected']);
            }
        }        
        $attributes = array();
        if(isset($params['attr']) && is_array($params['attr'])){
            foreach ($params['attr'] as $attrKey => $attrValue) {
                $attributes[] = $attrKey . '="' . $this->view->escape($attrValue) . '"';
            }
        }
        $attributes = implode(" ", $attributes);
        $html = '<select id="' . $selectId . '" name="' . $selectName . '" ' . $attributes . '>';
        if(isset($params['default_text'])){
            $html .= '<option value="" selected="selected">' . $this->view->translate($params['default_text']) . '</option>';
        }
        /* @var $menuItem Cms_Model_MenuItem */
        foreach ($menuItems as $menuItem) {
            $menuPad = str_pad('', $menuItem->get_level() * 6 * 5, '&nbsp;', STR_PAD_LEFT);
            $selected = in_array($menuItem->get_id(), $selectedValues) ? ' selected="selected"' : '';
            $html .= '<option ' . $selected . ' value="' . $menuItem->get_id() . '" >' . $menuPad . $this->view->escape($menuItem->get_name()) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
    
    protected function _populateMenuItems(&$menuItems){
        $menus = Cms_Model_MenuMapper::getInstance()->getMenus();
        foreach ($menus as $menu) {
            $zendMenus = Cms_Model_MenuItemMapper::getInstance()->fetchZendNavigationArray(array(
                'lang' => CURR_LANG,
                'menu' => $menu['code']
            ));    
            $this->_flattenMenu($zendMenus, $menuItems);            
        }         
    }
    
    protected function _flattenMenu($pages, &$flatArray, $level = 0){
        uasort($pages, function($a, $b){
            $aOrder = (int)$a['order'];
            $bOrder = (int)$b['order'];
            if ($aOrder == $bOrder) {
            return 0;
            }
            return ($aOrder < $bOrder) ? -1 : 1;            
        });
        foreach ($pages as $page) {
            $menuItem = $page['entity'];
            $menuItem->set_level($level);
            $flatArray[] = $menuItem;
            if(isset($page['pages']) && count($page['pages'])){
                $this->_flattenMenu($page['pages'], $flatArray, $level + 1);
            }
        }
        return;
    }    
}
