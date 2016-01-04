<?php
class Zend_View_Helper_FbLike extends Zend_View_Helper_Abstract {

    const LAYOUT_BTN    = 'button';
    const LAYOUT_BOXCNT = 'box_count';
    const LAYOUT_BTNCNT = 'button_count';
    const LAYOUT_STND = 'standard';



    public $_view;
 
    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
    }
    
    public function fbLike($data_href = "", $data_width="400", $data_layout=self::LAYOUT_BTNCNT, $data_action="like", $data_show_faces="false", $data_share="false") {
        
        $like = '<div class="fb-like" data-href="' . $data_href . '" data-width="' . $data_width . '" data-layout="' . $data_layout . '" data-action="' . $data_action . '" data-show-faces="' . $data_show_faces . '" data-share="' . $data_share . '"></div>';
        echo $like;

        return true;
    }
    
   
}
?>
   