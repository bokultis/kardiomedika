<?php
/**
 * MenuEntity class
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Cms_Model_Menu extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_code;
    protected $_name;
   
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

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }


}