<?php

/**
 * GA tag implementation
 *
 * @package HCMS
 * @subpackage Tags
 * @copyright Horisen
 * @author milan
 */
class HCMS_Tags_GaTag extends HCMS_Tags_BaseTag
{
    protected $name = 'Google Analytics';
    protected $positions = array(HCMS_Tags_TagManager::POS_BODY_END);
    
    public function getTag($settings, $position)
    {
        return "
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '" . $settings['tracking_id'] . "', 'auto');
  ga('send', 'pageview');

</script>
            ";        
    }

}