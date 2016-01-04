<?php

/**
 * GMT tag implementation
 *
 * @package HCMS
 * @subpackage Tags
 * @copyright Horisen
 * @author boris
 */
class HCMS_Tags_GtmTag extends HCMS_Tags_BaseTag
{
    protected $name = 'Google Tag Manager';
    protected $positions = array(HCMS_Tags_TagManager::POS_BODY_START);
    
    public function getTag($settings, $position)
    {
                $src = 'src="//www.googletagmanager.com/ns.html?id='. $settings['container_id'] .'" '
                . 'height="0" width="0" style="display:none;visibility:hidden"';
        
        return "
<!-- Google Tag Manager -->
<noscript><iframe ". $src ."></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" . $settings['container_id'] . "');</script>
<!-- End Google Tag Manager -->
            ";       
    }

}