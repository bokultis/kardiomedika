<?php
/**
 * View helper which loads facebook resources/js, etc
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_FacebookRes extends Zend_View_Helper_Abstract {
    /**
     * Get facebook resources // js, html
     *
     * @param Application_Model_Application $app
     * @param array $config => connect, resize, https, resizeScript
     */
    public function facebookRes ($app, $config = array()) {
        $connect = isset ($config['connect'])? $config['connect'] : true;
        $resize = isset ($config['resize'])? $config['resize'] : true;
        $https = isset ($config['https'])? $config['https'] : true;

        if(!isset ($app) || (!$connect && !$resize)){
            return false;
        }
        $fbLocale = 'en_US';
        $signedRequest = $app->get_signed_request();
        if(isset ($signedRequest['user']['locale'])){
            $fbLocale = $signedRequest['user']['locale'];
        }
        $fbConnectFile = 'http';
        if($https){
            $fbConnectFile .= 's';
        }
        $fbConnectFile .= '://connect.facebook.net/' . $fbLocale . '/all.js';

        $resizeScript = '';
        if($resize){
            if(isset ($config['resizeScript'])){
                $resizeScript = $config['resizeScript'];
            }
            else{
                $resizeScript .= 'FB.Canvas.setAutoGrow();';
            }
        }

        $html = '
<div id="fb-root"></div>
<script type="text/javascript">
window.fbAsyncInit = function() {
    FB.init({
        appId  : \'' . $app->get_fb_settings('api_id') . '\',
        //status : true, // check login status
        //cookie : true, // enable cookies to allow the server to access the session
        xfbml  : true  // parse XFBML
    });

    ' . $resizeScript . '
};

(function(d){
    var js, id = \'facebook-jssdk\';
    if (d.getElementById(id)) {
        return; // already loaded, no need to load again
    }
    js = d.createElement(\'script\'); js.id = id; js.async = true;
    js.src = "' . $fbConnectFile . '";
    d.getElementsByTagName(\'head\')[0].appendChild(js);
}(document));
</script>
';
        return  $html;
    }
}