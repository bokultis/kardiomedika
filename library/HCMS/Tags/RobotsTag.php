<?php

/**
 * Robots tag
 *
 * @package HCMS
 * @subpackage Tags
 * @copyright Horisen
 * @author boris
 */
class HCMS_Tags_RobotsTag extends HCMS_Tags_BaseTag
{
    protected $name = 'Google Tag Manager';
    protected $positions = array(HCMS_Tags_TagManager::POS_HEAD);
    
    public function getTag($settings, $position)
    {
        return '
<meta name="robots" content="noindex, nofollow, nosnippet, noodp, noarchive, noimageindex" />
<meta name="googlebot" content="noindex, nofollow, nosnippet, noodp, noarchive, noimageindex" />
';       
    }

}