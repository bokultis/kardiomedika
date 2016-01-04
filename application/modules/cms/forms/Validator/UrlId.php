<?php
/**
 * Unique URI validator
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author milan
 */
class Cms_Form_Validator_UrlId extends Zend_Validate_Abstract
{
    const URLID_EXISTS = 'INV_REL_INT';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::URLID_EXISTS => 'The same URL Id already exists in some other page.'
    );

    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $dbAdapter = null;
    /**
     * data
     *
     * @var mixed
     */
    protected $_data;

    /**
     * value
     *
     * @var mixed
     */
    protected $_value;

    protected $_lang;


    /**
     * Sets validator options
     *
     * @param  array $data
     * @param string $lang
     * @return void
     */
    public function __construct($data,$lang = null){
        $this->_data = $data;
        $this->_lang = $lang;

        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
    }

    public function isValid($value, $context = null){
        $result = true;
        $this->_value = $value;

        $where = array();
        $where[] = $this->dbAdapter->quoteInto("(p.url_id = ? OR pt.url_id = ?)", $value);
        if(isset ($this->_data['id'])){
            $where[] = $this->dbAdapter->quoteInto("(p.id <> ?)", $this->_data['id']);
        }
        if(isset ($this->_data['type_id'])){
            $where[] = $this->dbAdapter->quoteInto("(p.type_id = ?)", $this->_data['type_id']);
        }
        $where = implode(' AND ', $where);
        $sql = "SELECT COUNT(p.id) AS doubles
                FROM cms_page AS p
                    LEFT JOIN cms_page_tr AS pt ON (p.id = pt.translation_id AND pt.language = '" . $this->_lang . "')
                WHERE $where";

        //echo "\n\n$sql\n\n"; die();
        $resultSet = $this->dbAdapter->fetchAll($sql);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet[0];
        $result = ($row['doubles'] == 0);

        if(!$result){
            $this->_error(self::URLID_EXISTS);
        }
        return $result;
    }
}