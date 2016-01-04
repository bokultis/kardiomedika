<?php

/**
 * Webmaster tools meta tag verification
 *
 * @package HCMS
 * @subpackage Tags
 * @copyright Horisen
 * @author boris
 */
class HCMS_Tags_WmtTag extends HCMS_Tags_BaseTag
{
    protected $name = 'Webmaster Tools';
    protected $positions = array(HCMS_Tags_TagManager::POS_HEAD);
    
    public function getTag($settings, $position)
    {
        return $settings['meta'];    
    }

}