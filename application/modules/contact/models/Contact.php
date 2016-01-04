<?php
/**
 * Contact Entity class
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Model_Contact extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_application_id;
    protected $_posted;
    protected $_gender;
    protected $_first_name;
    protected $_last_name;
    protected $_company;
    protected $_fileupload;
    protected $_email;
    protected $_zip;
    protected $_street;
    protected $_city;
    protected $_country;
    protected $_phone;
    protected $_mobile;
    protected $_fax;
    protected $_subject;
    protected $_description;
    protected $_message;
    protected $_language;
    protected $_form_id;

    function get_form_id() {
        return $this->_form_id;
    }

    function set_form_id($_form_id) {
        $this->_form_id = $_form_id;
        return $this;
    }
    
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

    public function get_posted() {
        return $this->_posted;
    }

    public function set_posted($_posted) {
        $this->_posted = $_posted;
        return $this;
    }
    
        public function get_gender() {
        return $this->_gender;
    }

    public function set_gender($_gender) {
        $this->_gender = $_gender;
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
    
    public function get_company() {
        return $this->_company;
    }

    public function set_company($_company) {
        $this->_company = $_company;
        return $this;
    }
    
        public function get_fileupload() {
        return $this->_fileupload;
    }

    public function set_fileupload($_fileupload) {
        $this->_fileupload = $_fileupload;
        return $this;
    }

    
    public function get_email() {
        return $this->_email;
    }

    public function set_email($_email) {
        $this->_email = $_email;
        return $this;
    }

    public function get_country() {
        return $this->_country;
    }

    public function set_country($_country) {
        $this->_country = $_country;
        return $this;
    }
    
    public function get_zip() {
        return $this->_zip;
    }

    public function set_zip($_zip) {
        $this->_zip = $_zip;
        return $this;
    }
    
    public function get_city() {
        return $this->_city;
    }

    public function set_city($_city) {
        $this->_city = $_city;
        return $this;
    }
    
    public function get_street() {
        return $this->_street;
    }

    public function set_street($_street) {
        $this->_street = $_street;
        return $this;
    }

    public function get_phone() {
        return $this->_phone;
    }

    public function set_phone($_phone) {
        $this->_phone = $_phone;
        return $this;
    }

    public function get_mobile() {
        return $this->_mobile;
    }

    public function set_mobile($_mobile) {
        $this->_mobile = $_mobile;
        return $this;
    }

    public function get_fax() {
        return $this->_fax;
    }

    public function set_fax($_fax) {
        $this->_fax = $_fax;
        return $this;
    }

    public function get_subject() {
        return $this->_subject;
    }

    public function set_subject($_subject) {
        $this->_subject = $_subject;
        return $this;
    }

    public function get_description() {
        return $this->_description;
    }

    public function set_description($_description) {
        $this->_description = $_description;
        return $this;
    }
    
    public function get_message() {
        return $this->_message;
    }

    public function set_message($_message) {
        $this->_message = $_message;
        return $this;
    }    

    public function get_language() {
        return $this->_language;
    }

    public function set_language($_language) {
        $this->_language = $_language;
        return $this;
    }
}