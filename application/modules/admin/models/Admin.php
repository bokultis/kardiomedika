<?php
/**
 * Admin_Model Admin
 *
 * @package Modules
 * @subpackage Admin_Model
 * @copyright Horisen
 * @author zeka
 */
class Admin_Model_Admin extends HCMS_Model_Entity {

    protected $_camelCase = false;

    protected $_id;
    protected $_username;
    protected $_password;
    protected $_status;
    protected $_status_dt;
    protected $_level;


    public function  __toString() {
        return "Admin[
            id=$this->_id,
            username=$this->_username,
            password=$this->_password,
            status=$this->_status,
            status_dt=$this->_status_dt,
            level=$this->_level
        ]";
    }

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_username() {
        return $this->_username;
    }

    public function set_username($_username) {
        $this->_username = $_username;
        return $this;
    }

    public function get_password() {
        return $this->_password;
    }

    public function set_password($_password) {
        $this->_password = $_password;
        return $this;
    }

    public function get_status() {
        return $this->_status;
    }

    public function set_status($_status) {
        $this->_status = $_status;
        return $this;
    }

    public function get_status_dt() {
        return $this->_status_dt;
    }

    public function set_status_dt($_status_dt) {
        $this->_status_dt = $_status_dt;
        return $this;
    }

    public function get_level() {
        return $this->_level;
    }

    public function set_level($_level) {
        $this->_level = $_level;
        return $this;
    }

}
?>
