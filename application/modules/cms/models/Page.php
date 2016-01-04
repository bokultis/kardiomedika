<?php
/**
 * Page Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_Page extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_code;
    protected $_application_id;
    protected $_title;
    protected $_content;
    protected $_type_id;
    protected $_format;
    protected $_status;
    protected $_teaser;
    protected $_data;
    protected $_meta;
    protected $_user_id;
    protected $_posted;
    protected $_url_id;

    protected $_type_name;
    protected $_user_name;

    protected $_extension;
    
    protected $_category_id;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_code() {
        return $this->_code;
    }

    public function set_code($_code) {
        $this->_code = $_code;
        return $this;
    }

    public function get_application_id() {
        return $this->_application_id;
    }

    public function set_application_id($_application_id) {
        $this->_application_id = $_application_id;
        return $this;
    }

    public function get_title() {
        return $this->_title;
    }

    public function set_title($_title) {
        $this->_title = $_title;
        return $this;
    }

    public function get_content() {
        return $this->_content;
    }

    public function set_content($_content) {
        $this->_content = $_content;
        return $this;
    }

    public function get_type_id() {
        return $this->_type_id;
    }

    public function set_type_id($_type_id) {
        $this->_type_id = $_type_id;
        return $this;
    }

    public function get_format() {
        return $this->_format;
    }

    public function set_format($_format) {
        $this->_format = $_format;
        return $this;
    }

    public function get_status() {
        return $this->_status;
    }

    public function set_status($_status) {
        $this->_status = $_status;
        return $this;
    }

    public function get_teaser() {
        return $this->_teaser;
    }

    public function set_teaser($_teaser) {
        $this->_teaser = $_teaser;
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

    public function get_user_id() {
        return $this->_user_id;
    }

    public function set_user_id($_user_id) {
        $this->_user_id = $_user_id;
        return $this;
    }

    public function get_posted() {
        return $this->_posted;
    }

    public function set_posted($_posted) {
        $this->_posted = $_posted;
        return $this;
    }

    public function get_type_name() {
        return $this->_type_name;
    }

    public function set_type_name($_type_name) {
        $this->_type_name = $_type_name;
        return $this;
    }

    public function get_user_name() {
        return $this->_user_name;
    }

    public function set_user_name($_user_name) {
        $this->_user_name = $_user_name;
        return $this;
    }

    public function get_url_id() {
        return $this->_url_id;
    }

    public function set_url_id($_url_id) {
        $this->_url_id = $_url_id;
        return $this;
    }

    public function get_extension() {
        return $this->_extension;
    }

    public function set_extension($_extension) {
        $this->_extension = $_extension;
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