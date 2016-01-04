<?php

/**
 * Tag manager factory
 *
 * @package HCMS
 * @subpackage Tags
 * @copyright Horisen
 * @author milan
 */
class HCMS_Tags_TagManager
{
    const POS_HEAD = 'head';
    const POS_BODY_START = 'body_start';    
    const POS_BODY_END = 'body_end';
    
    protected $tagObjects = array();
    /**
     * Get tag object
     * 
     * @param string $tagKey
     * @throws Exception
     * @return HCMS_Tags_TagInterface
     */
    public function getTagObject($tagKey)
    {
        if(isset($this->tagObjects[$tagKey])){
            return $this->tagObjects[$tagKey];
        }
        $className = 'HCMS_Tags_' . ucfirst($tagKey). 'Tag';
        if(!class_exists($className)){
            throw new Exception("Class [$className] not found");
        }
        $this->tagObjects[$tagKey] = new $className();
        return $this->tagObjects[$tagKey];
    }
    
    public function getTagsHtml($settings, $position)
    {
        $html = '';
        if(!$settings || !is_array($settings)){
            return $html;
        }
        foreach ($settings as $tagKey => $tagSettings) { 
            if(!isset($tagSettings['active']) || !$tagSettings['active']){
                continue;
            }     
            $tagObject = $this->getTagObject($tagKey);
            if(!in_array($position, $tagObject->getPositions())){
                continue;
            }            
            $html .= $tagObject->getTag($tagSettings, $position);
        }
        return $html;
    }
}
