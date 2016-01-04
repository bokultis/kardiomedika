<?php
/**
 * Page Mapper
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_PageMapper extends HCMS_Model_Mapper_Page {
    /**
     * singleton instance
     *
     * @var Cms_Model_PageMapper
     */
    protected static $_instance = null;


    /**
     * get instance
     *
     *
     * @return Cms_Model_PageMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}