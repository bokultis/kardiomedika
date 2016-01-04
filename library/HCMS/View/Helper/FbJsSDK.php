<?php
class Zend_View_Helper_FbJsSDK extends Zend_View_Helper_Abstract {
    
    public function fbJsSDK($appId = 151673644938727) {
        
        $jsSDK = '
            <div id="fb-root"></div>
            <script>(function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId='. $appId .'";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));</script>';

         echo $jsSDK;
         return true;
    }
    
   
}
?>
   