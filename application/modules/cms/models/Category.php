<?php

/**
 * Category Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author zeka
 */
class Cms_Model_Category extends HCMS_Model_Entity {

    protected $_camelCase = false;

    protected $_id;
    protected $_url_id;
    protected $_set_id;
    protected $_name;
    protected $_description;
    protected $_parent_id;
    protected $_level;
    protected $_data;
    protected $_meta;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_url_id() {
        return $this->_url_id;
    }

    public function set_url_id($_url_id) {
        $this->_url_id = $_url_id;
        return $this;
    }

    public function get_set_id() {
        return $this->_set_id;
    }

    public function set_set_id($_set_id) {
        $this->_set_id = $_set_id;
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

    public function get_parent_id() {
        return $this->_parent_id;
    }

    public function set_parent_id($_parent_id) {
        $this->_parent_id = $_parent_id;
        return $this;
    }

    public function get_level() {
        return $this->_level;
    }

    public function set_level($_level) {
        $this->_level = $_level;
        return $this;
    }

    public function get_data($key = null) {
        if (!isset($key)) {
            return $this->_data;
        } else {
            if (isset($this->_data[$key])) {
                return $this->_data[$key];
            } else {
                return null;
            }
        }
    }

    public function set_data($_data) {
        $this->_data = $_data;
        return $this;
    }

    public function get_meta($key = null) {
        if(!isset ($key)){
            return $this->_meta;
        }
        else{
            if(isset ($this->_meta[$key])){
                return $this->_meta[$key];
            }
            else{
                return null;
            }
        }
    }

    public function set_meta($_meta) {
        $this->_meta = $_meta;
        return $this;
    }

}