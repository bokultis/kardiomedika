<?php

/**
 * Category Set Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Cms_Model_CategorySet extends HCMS_Model_Entity {

    protected $_camelCase = false;

    protected $_id;
    protected $_name;
    protected $_description;
    protected $_module;
    protected $_page_type_id;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }

    public function get_description() {
        return $this->_description;
    }

    public function set_description($_description) {
        $this->_description = $_description;
        return $this;
    }

    public function get_module() {
        return $this->_module;
    }

    public function set_module($_module) {
        $this->_module = $_module;
        return $this;
    }

    public function get_page_type_id() {
        return $this->_page_type_id;
    }

    public function set_page_type_id($_page_type_id) {
        $this->_page_type_id = $_page_type_id;
        return $this;
    }

    
}