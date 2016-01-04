<?php
/**
 * Teaser Entity class
 *
 * @package Teaser
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Teaser_Model_Teaser extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;    
    protected $_box_code;
    protected $_name;
    
    protected $_items = array();
    
    protected $_menu_item_ids = array();
    
    protected $_all_menu_items;
    
    protected $_content;
  
    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_box_code() {
        return $this->_box_code;
    }

    public function set_box_code($_box_code) {
        $this->_box_code = $_box_code;
        return $this;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }
    
    public function get_items() {
        return $this->_items;
    }

    public function set_items($_items) {
        $this->_items = $_items;
        return $this;
    }
    
    public function get_menu_item_ids() {
        return $this->_menu_item_ids;
    }

    public function set_menu_item_ids($_menu_item_ids) {
        $this->_menu_item_ids = $_menu_item_ids;
        return $this;
    }
    
    public function get_all_menu_items() {
        return $this->_all_menu_items;
    }

    public function set_all_menu_items($_all_menu_items) {
        $this->_all_menu_items = $_all_menu_items;
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

}