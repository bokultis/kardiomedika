<?php
/**
 * Render picture tag
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_RenderPicture extends Zend_View_Helper_Abstract {
    
    protected static $imagesConf = null;

    /**
     * Render picture tag
     *
     * @param string $imageSlug image ID
     * @param string $directory directory path
     * @param string $altText alt text
     * @param string $fileExt file extension including dot
     * @param array $params extra params
     * @return string
     */
    public function renderPicture($imageSlug, $directory, $altText = '', $fileExt = '.jpg', $params = array()) {
        if(!isset(self::$imagesConf)){
            self::$imagesConf = HCMS_Utils::loadThemeConfig('picture.php');        
        }
        
        $section = isset($params['section'])? $params['section']: 'default';
        $images = self::$imagesConf[$section];        
        if(isset($params['css_class'])){
            $cssClass = ' class="' . $params['css_class'] . '"';
        }
        else{
            $cssClass = '';
        }
        $html = '<picture' . $cssClass . '><!--[if IE 9]><video style="display: none;"><![endif]-->';
        foreach ($images as $query => $suffix) {
            $path = $directory . '/' . $imageSlug . '_' . $suffix . $fileExt;
            if(isset($params['version'])){
                $path .= '?v=' . $params['version'];
            }
            $html .= '<source srcset="' . $path . ' " media="' . $query . '">';
        }
        $html .= '<!--[if IE 9]></video><![endif]--><img srcset="' . $path . '" alt="' . $this->view->escape($altText) . '"></picture>';
        return $html;
    }
}
