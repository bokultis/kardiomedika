<?php
/**
 * Page Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_PageType extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_code;
    protected $_name;
    protected $_description;
    protected $_module;
    protected $_data;

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

    public function get_code() {
        return $this->_code;
    }

    public function set_code($_code) {
        $this->_code = $_code;
        return $this;
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

    public function get_data($key = null) {
        if(!isset ($key)){
            return $this->_data;
        }
        else{
            if(isset ($this->_data[$key])){
                return $this->_data[$key];
            }
            else{
                return null;
            }
        }
    }

    public function set_data($_data) {
        $this->_data = $_data;
        return $this;
    }
}