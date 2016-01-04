<?php

class Application_Model_Country extends HCMS_Model_Entity
{
    protected $_camelCase = false;

    protected $_id;
    protected $_name;
    protected $_name_de;
    protected $_code2;
    protected $_code3;
    protected $_domain;
    protected $_dial_code;
    protected $_currency;
    protected $_def_lang;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = $_id;
        return $this;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }

    public function get_name_de() {
        return $this->_name_de;
    }

    public function set_name_de($_name_de) {
        $this->_name_de = $_name_de;
        return $this;
    }

    public function get_code2() {
        return $this->_code2;
    }

    public function set_code2($_code2) {
        $this->_code2 = $_code2;
        return $this;
    }

    public function get_code3() {
        return $this->_code3;
    }

    public function set_code3($_code3) {
        $this->_code3 = $_code3;
        return $this;
    }

    public function get_domain() {
        return $this->_domain;
    }

    public function set_domain($_domain) {
        $this->_domain = $_domain;
        return $this;
    }

    public function get_dial_code() {
        return $this->_dial_code;
    }

    public function set_dial_code($_dial_code) {
        $this->_dial_code = $_dial_code;
        return $this;
    }

    public function get_currency() {
        return $this->_currency;
    }

    public function set_currency($_currency) {
        $this->_currency = $_currency;
        return $this;
    }

    public function get_def_lang() {
        return $this->_def_lang;
    }

    public function set_def_lang($_def_lang) {
        $this->_def_lang = $_def_lang;
        return $this;
    }



}

