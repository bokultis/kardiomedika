<?php
/**
 * Custom tag base implementation
 *
 * @package HCMS
 * @subpackage Tags
 * @copyright Horisen
 * @author milan
 */
abstract class HCMS_Tags_BaseTag implements HCMS_Tags_TagInterface
{
    protected $name = 'Generic tag';
    protected $positions = array();


    public function getName()
    {
        return $this->name;
    }

    public function getPositions()
    {
        return $this->positions;
        
    }
}
