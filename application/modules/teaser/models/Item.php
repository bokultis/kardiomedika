<?php
/**
 * Item Entity class
 *
 * @package Teaser
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Teaser_Model_Item extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_box_id;
    protected $_fallback;
    protected $_start_dt;
    protected $_end_dt;
    protected $_title;
    protected $_content_type;
    protected $_content;
    
    protected $_max_width;
    protected $_max_height;


    protected $_template;
    protected $_code;
    protected $_box_code;
    
    protected $_order_num;
    
    protected $_teaser_ids = array();
    
    protected $_item_template;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_box_id() {
        return $this->_box_id;
    }

    public function set_box_id($_box_id) {
        $this->_box_id = $_box_id;
        return $this;
    }

    public function get_fallback() {
        return $this->_fallback;
    }

    public function set_fallback($_fallback) {
        $this->_fallback = $_fallback;
        return $this;
    }

    public function get_start_dt() {
        return $this->_start_dt;
    }

    public function set_start_dt($_start_dt) {
        $this->_start_dt = $_start_dt;
        return $this;
    }

    public function get_end_dt() {
        return $this->_end_dt;
    }

    public function set_end_dt($_end_dt) {
        $this->_end_dt = $_end_dt;
        return $this;
    }

    public function get_title() {
        return $this->_title;
    }

    public function set_title($_title) {
        $this->_title = $_title;
        return $this;
    }

    public function get_content_type() {
        return $this->_content_type;
    }

    public function set_content_type($_content_type) {
        $this->_content_type = $_content_type;
        return $this;
    }

    public function get_content($key = null) {
        if(!isset ($key)){
            return $this->_content;
        }
        else{
            if(isset ($this->_content[$key])){
                return $this->_content[$key];
            }
            else{
                return null;
            }
        }
    }

    public function set_content($_content) {
        $this->_content = $_content;
        return $this;
    }
    
    public function get_template() {
        return $this->_template;
    }

    public function set_template($_template) {
        $this->_template = $_template;
        return $this;
    }

    public function get_max_width() {
        return $this->_max_width;
    }

    public function set_max_width($_max_width) {
        $this->_max_width = $_max_width;
        return $this;
    }

    public function get_max_height() {
        return $this->_max_height;
    }

    public function set_max_height($_max_height) {
        $this->_max_height = $_max_height;
        return $this;
    }
    
    public function get_code() {
        return $this->_code;
    }

    public function set_code($_code) {
        $this->_code = $_code;
        return $this;
    }   
    
    public function get_order_num() {
        return $this->_order_num;
    }

    public function set_order_num($_order_num) {
        $this->_order_num = $_order_num;
        return $this;
    }
    
    public function get_box_code() {
        return $this->_box_code;
    }

    public function set_box_code($_box_code) {
        $this->_box_code = $_box_code;
        return $this;
    }
    
    public function get_teaser_ids() {
        return $this->_teaser_ids;
    }

    public function set_teaser_ids($_teaser_ids) {
        $this->_teaser_ids = $_teaser_ids;
        return $this;
    }
    
    public function get_item_template() {
        return $this->_item_template;
    }

    public function set_item_template($_item_template) {
        $this->_item_template = $_item_template;
        return $this;
    }
}