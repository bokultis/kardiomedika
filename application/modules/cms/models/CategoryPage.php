<?php

/**
 * Category Page Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Cms_Model_CategoryPage extends HCMS_Model_Entity {

    protected $_camelCase = false;

    protected $_id;
    protected $_category_id;
    protected $_page_id;


    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_category_id() {
        return $this->_category_id;
    }

    public function set_category_id($_category_id) {
        $this->_category_id = $_category_id;
        return $this;
    }

    public function get_page_id() {
        return $this->_page_id;
    }

    public function set_page_id($_page_id) {
        $this->_page_id = $_page_id;
        return $this;
    }



}