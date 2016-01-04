<?php
class Zend_View_Helper_FbShare extends Zend_View_Helper_Abstract {

    const LAYOUT_BTN    = 'button';
    const LAYOUT_BOXCNT = 'box_count';
    const LAYOUT_BTNCNT = 'button_count';
    const LAYOUT_ICNLNK = 'icon_link';
    const LAYOUT_ICN = 'icon';
    const LAYOUT_LNK = 'link';
 
    public $_view;
 
    public function setView(Zend_View_Interface $view)
    {
        $this->_view = $view;
    }
    
    public function fbShare($data_href = "", $data_width="400", $data_type= self::LAYOUT_BTN) {
        
        $share = '<div class="fb-share-button" data-href="' . $data_href . '" data-width="' . $data_width . '" data-type="' . $data_type . '"></div>';
        echo $share;

        return true;
    }
    
   
}
?>
   