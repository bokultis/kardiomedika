<?php
/**
 * Base Maper model.
 *
 * @package HCMS
 * @subpackage Model
 * @copyright Horisen
 * @author milan
 */
class HCMS_Model_Table extends Zend_Db_Table_Abstract {

    /**
     * @param  mixed $db Either an Adapter object, or a string naming a Registry key
     * @return Zend_Db_Table_Abstract Provides a fluent interface
     */
    public function setAdapter($db){
        return $this->_setAdapter($db);
    }
}
