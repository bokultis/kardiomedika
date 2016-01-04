<?php
/**
 * Translation Menu Domain Model
 *
 * @package Translation
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Translation_Model_Menu extends HCMS_Model_Entity
{
    protected $_camelCase = false;
    
    protected $_id;
    protected $_name;

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
}
