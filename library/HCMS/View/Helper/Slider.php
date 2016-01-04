<?php
/**
 * Slider view helper - create slider wher it's needed
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author Boris
 *
 */
class HCMS_View_Helper_Slider extends Zend_View_Helper_Abstract {

    /**
     * Theme Path view helper - prefixes theme path
     *
     * @param string $path Path without theme
     * @return string
     */
    public function slider($given_code) {

        $criteria = array(
                'lang'          => CURR_LANG,
                'type_code'       => 'slider'
        );
        $slider_images = Cms_Model_PageMapper::getInstance()->fetchAll($criteria);
        //Zend_Debug::dump($slider_images);
        $slider_show_images = array();
        $order = array();
        if(count($slider_images) > 0){
            foreach ($slider_images as $key => $slider_image) {
                $toShow = $slider_image->get_data('show');              
                if(isset($toShow) && $toShow > 0){
                    $order[$key] = $slider_image->get_data('order');
                    $slider_show_images[] = $slider_image->toArray();
                }
            }
        }
        array_multisort($order, SORT_ASC, $slider_show_images);
        
       $slider_images = $this->sortByCode($slider_show_images, $given_code);
        
        //$this->view->slider_show_images = $slider_show_images;
         
        $slider = "";
        
        if(count($slider_images) > 0){
             
            $slider .= '<div class="slider">
                            <ul class="bxslider">';
            foreach ($slider_images as $slider_show_image) {
                $target = $slider_show_image['data']['target'];
                if(!isset($target) || $target == ''){
                    $target = "javascript:void(0);";
                }
                $slider_teaser = '';
                if(isset($slider_show_image['data']['teaser']) && $slider_show_image['data']['teaser'] != ''){
                  $slider_teaser = '<div class="sliderTeaserContainer"><div class="slider_teaser">'. $slider_show_image['data']['teaser'].'</div></div>';  
                }
                
                $slider .= '<li style="background:url(../1/'. $slider_show_image['data']['img'] .') no-repeat center center; height: 350px;"><a href="' . $target . '" target="_blank" class="slider_link"></a>' . $slider_teaser . '</li>';
            }
            
              $slider .= "</ul>
                    </div>";
            
            }
        return $slider;
        
    }
    
    protected function sortByCode($slider, $given_code){
        $tmp_slider = array();
        foreach($slider as $k => $s){
            if($given_code != '' && $s['code'] == $given_code){
                $tmp_slider[] =  $s;
                //unset($slider[$k]);
                //array_unshift($slider, $s);
            }
        }
        
        if(!count($tmp_slider)){
            return $slider;
        }
        
        return $tmp_slider;        
    }
}
