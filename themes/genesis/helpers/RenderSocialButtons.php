<?php
/**
 * Custom api page renderer
 *
 * @package Themes
 * @subpackage Helpers
 * @copyright Horisen
 * @author milan
 *
 */
class Theme_View_Helper_RenderSocialButtons extends HCMS_View_Helper_RenderPage {

    /**
     * Get social buttons html
     *
     * @return string
     */
    public function renderSocialButtons() {
        //facebook script
        $this->view->headScript()->appendScript('
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=134875086608004&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));');
        //twitter script
        $this->view->headScript()->appendScript("!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');");
        //google script
        $this->view->headScript()->setAllowArbitraryAttributes(true);
        $this->view->headScript()->appendFile('https://apis.google.com/js/platform.js', null, array('async' => 'async','defer' => 'defer'));
        //pinterest script
        $this->view->headScript()->appendFile('//assets.pinterest.com/js/pinit.js', null, array('async' => 'async','defer' => 'defer'));
        return '
<div class="fb-like" data-layout="button" data-action="like" data-show-faces="false" data-share="false" style="margin-right:5px; display: inline-block; vertical-align:top;"></div>

<div class="g-plusone" data-annotation="none" data-size="medium" data-width="300"></div>

<a href="https://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a>

<a href="//www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"  data-pin-color="red"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_20.png" /></a>
';        
    }
}