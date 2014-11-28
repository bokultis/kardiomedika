<?php
/**
 * Module Entity class
 *
 * @package Application
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Application_Model_Module extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    protected $_id;
    protected $_application_id;
    protected $_code;
    protected $_name;
    protected $_description;
    protected $_settings;
    protected $_data;


    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_application_id() {
        return $this->_application_id;
    }

    public function set_application_id($_application_id) {
        $this->_application_id = $_application_id;
        return $this;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }

    public function get_settings($key = null) {
        if(!isset ($key)){
            return $this->_settings;
        }
        else{
            if(isset ($this->_settings[$key])){
                return $this->_settings[$key];
            }
            else{
                return null;
            }
        }
    }

    public function set_settings($_settings) {
        $this->_settings = $_settings;
        return $this;
    }

    public function get_code() {
        return $this->_code;
    }

    public function set_code($_code) {
        $this->_code = $_code;
        return $this;
    }

    public function get_description() {
        return $this->_description;
    }

    public function set_description($_description) {
        $this->_description = $_description;
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