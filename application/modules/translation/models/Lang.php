<?php
/**
 * Translation Lang Domain Model
 *
 * @package Translation
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Translation_Model_Lang extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    protected $_id;
    protected $_code;
    protected $_name;
    protected $_default;
    protected $_front_enabled;

    public function get_id() {
        return $this->_id;
    }

    public function set_id($_id) {
        $this->_id = (int)$_id;
        return $this;
    }

    public function get_name() {
        return $this->_name;
    }

    public function set_name($_name) {
        $this->_name = $_name;
        return $this;
    }
    public function get_code() {
        return $this->_code;
    }

    public function set_code($_code) {
        $this->_code = $_code;
        return $this;
    }

    public function get_default() {
        return $this->_default;
    }

    public function set_default($_default) {
        $this->_default = $_default;
        return $this;
    }

    public function get_front_enabled() {
        return $this->_front_enabled;
    }

    public function set_front_enabled($_front_enabled) {
        $this->_front_enabled = $_front_enabled;
        return $this;
    }


}
