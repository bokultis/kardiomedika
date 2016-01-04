<?php

/**
 * Custom tag interface
 *
 * @package HCMS
 * @subpackage Tags
 * @copyright Horisen
 * @author milan
 */
interface HCMS_Tags_TagInterface
{
    public function getName();
    public function getPositions();
    public function getTag($settings, $position);
}
