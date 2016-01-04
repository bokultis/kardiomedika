<?php
/**
 * MenuItem Entity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Cms_Model_MenuItem extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_application_id;
    protected $_menu;
    protected $_level;
    protected $_parent_id;
    protected $_name;
    protected $_route;
    protected $_path;
    protected $_uri;
    protected $_ord_num;
    protected $_params;
    protected $_params_old;
    protected $_page_id;
    protected $_page_id_new;
    protected $_meta;
    protected $_hidden;
    protected $_target;
    
    protected $_route_uri;

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

    public function get_menu() {
        return $this->_menu;
    }

    public function set_menu($_menu) {
        $this->_menu = $_menu;
        return $this;
    }

    public function get_level() {
        return $this->_level;
    }

    public function set_level($_level) {
        $this->_level = $_level;
        return $this;
    }

    public function get_parent_id() {
        return $this->_parent_id;
    }

    public function set_parent_id($_parent_id) {
        $this->_parent_id = $_parent_id;
        return $this;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }

    public function get_route() {
        return $this->_route;
    }

    public function set_route($_route) {
        $this->_route = $_route;
        return $this;
    }

    public function get_path() {
        return $this->_path;
    }

    public function set_path($_path) {
        $this->_path = $_path;
        return $this;
    }

    public function get_uri() {
        return $this->_uri;
    }

    public function set_uri($_uri) {
        $this->_uri = $_uri;
        return $this;
    }

    public function get_ord_num() {
        return $this->_ord_num;
    }

    public function set_ord_num($_ord_num) {
        $this->_ord_num = $_ord_num;
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
    public function get_page_id_new() {
        return $this->_page_id_new;
    }

    public function set_page_id_new($_page_id_new) {
        $this->_page_id_new = $_page_id_new;
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
    
    public function get_hidden() {
        return $this->_hidden;
    }

    public function set_hidden($_hidden) {
        $this->_hidden = $_hidden;
        return $this;
    }
    
     public function get_route_uri() {
        return $this->_route_uri;
    }

    public function set_route_uri($_route_uri) {
        $this->_route_uri = $_route_uri;
        return $this;
    }

    public function get_target() {
        return $this->_target;
    }

    public function set_target($_target) {
        $this->_target = $_target;
        return $this;
    }
    
    public function get_params_old() {
        return $this->_params_old;
    }

    public function set_params_old($_params_old) {
        $this->_params_old = $_params_old;
         return $this;
    }
}