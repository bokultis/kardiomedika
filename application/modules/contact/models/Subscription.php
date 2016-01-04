<?php
/**
 * Subscription Entity class
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Model_Subscription extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_first_name;
    protected $_last_name;
    protected $_email;
    protected $_code;
    protected $_status;
    protected $_subscribed_dt;
    protected $_unsubscribed_dt;
    protected $_gender;
    protected $_lang;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_first_name() {
        return $this->_first_name;
    }

    public function set_first_name($_first_name) {
        $this->_first_name = $_first_name;
        return $this;
    }

    public function get_last_name() {
        return $this->_last_name;
    }

    public function set_last_name($_last_name) {
        $this->_last_name = $_last_name;
        return $this;
    }

    public function get_email() {
        return $this->_email;
    }

    public function set_email($_email) {
        $this->_email = $_email;
        return $this;
    }
    
    public function get_code() {
        return $this->_code;
    }

    public function set_code($_code) {
        $this->_code = $_code;
        return $this;
    }

    public function get_status() {
        return $this->_status;
    }

    public function set_status($_status) {
        $this->_status = $_status;
        return $this;
    }

    public function get_subscribed_dt() {
        return $this->_subscribed_dt;
    }

    public function set_subscribed_dt($_subscribed_dt) {
        $this->_subscribed_dt = $_subscribed_dt;
        return $this;
    }

    public function get_unsubscribed_dt() {
        return $this->_unsubscribed_dt;
    }

    public function set_unsubscribed_dt($_unsubscribed_dt) {
        $this->_unsubscribed_dt = $_unsubscribed_dt;
        return $this;
    }

}