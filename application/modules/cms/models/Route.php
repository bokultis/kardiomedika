<?php
/**
 * Cms Route Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_Route extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;    
    protected $_application_id;
    protected $_uri;
    protected $_name;
    protected $_lang;
    protected $_path;
    protected $_params;
    protected $_page_id;
    protected $_page_title;

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

    public function get_uri() {
        return $this->_uri;
    }

    public function set_uri($_uri) {
        $this->_uri = $_uri;
        return $this;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }

    public function get_lang() {
        return $this->_lang;
    }

    public function set_lang($_lang) {
        $this->_lang = $_lang;
        return $this;
    }

    public function get_path() {
        return $this->_path;
    }

    public function set_path($_path) {
        $this->_path = $_path;
        return $this;
    }

    public function get_params() {
        return $this->_params;
    }

    public function set_params($_params) {
        $this->_params = $_params;
        return $this;
    }

    public function get_page_id() {
        return $this->_page_id;
    }

    public function set_page_id($_page_id) {
        $this->_page_id = $_page_id;
        return $this;
    }

    public function get_page_title() {
        return $this->_page_title;
    }

    public function set_page_title($_page_title) {
        $this->_page_title = $_page_title;
        return $this;
    }

}