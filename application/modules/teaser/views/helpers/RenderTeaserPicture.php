<?php
/**
 * View helper - renders picture tag of defined image version in teaser
 *
 * @package Teaser
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class Teaser_View_Helper_RenderTeaserPicture extends Zend_View_Helper_Abstract {
    
    
    public function renderTeaserPicture(Teaser_Model_Item $item, array $box, $params = array()) {
        if(!isset($box['params']['images'])){
            return false;
        }
        $boxImages = $box['params']['images'];
        if(isset($params['css_class'])){
            $cssClass = ' class="' . $params['css_class'] . '"';
        }
        else{
            $cssClass = '';
        }
        $html = '<picture' . $cssClass . '><!--[if IE 9]><video style="display: none;"><![endif]-->';
        foreach ($boxImages  as $imageKey => $imageDef) {
            $path = $item->get_content($imageKey);
            if(isset($params['version'])){
                $path .= '?v=' . $params['version'];
            }            
            $html .= '<source srcset="' . $path . ' " media="' . $imageDef['media_query'] . '">';
        }
        $html .= '<!--[if IE 9]></video><![endif]--><img srcset="' . $path . '" alt="' . $this->view->escape($item->get_content('image_alt')) . '"></picture>';
        return $html;        
    }      
}