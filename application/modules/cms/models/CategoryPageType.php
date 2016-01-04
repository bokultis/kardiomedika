<?php

/**
 * Category Page Type Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Cms_Model_CategoryPageType extends HCMS_Model_Entity {

    protected $_camelCase = false;

    protected $_id;
    protected $_set_id;
    protected $_type_id;
    protected $_category_id;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_set_id() {
        return $this->_set_id;
    }

    public function set_set_id($_set_id) {
        $this->_set_id = $_set_id;
        return $this;
    }

    public function get_type_id() {
        return $this->_type_id;
    }

    public function set_type_id($_type_id) {
        $this->_type_id = $_type_id;
        return $this;
    }

    public function get_category_id() {
        return $this->_category_id;
    }

    public function set_category_id($_category_id) {
        $this->_category_id = $_category_id;
        return $this;
    }

}