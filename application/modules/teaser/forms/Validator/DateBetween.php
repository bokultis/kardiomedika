<?php
/**
 * Unique URI validator
 *
 * @package Modules
 * @subpackage Teaser
 * @copyright Horisen
 * @author marko
 */
class Teaser_Form_Validator_DateBetween extends Zend_Validate_Abstract
{
    const PERIOD_OCCUPIED = 'PER_OCC';

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::PERIOD_OCCUPIED => 'Period is occupied by another item.'
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

    /**
     * Sets validator options
     *
     * @param  array $data
     * @param string $lang
     * @return void
     */
    public function __construct($data){
        $this->_data = $data;
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
    }

    public function isValid($value, $context = null){
        $result = true;
        $this->_value = $value;
        $whereAnd[] = $this->dbAdapter->quoteInto("ti.box_id = ? ", $this->_data['box_id']);
        if(isset ($this->_data['start_dt']) &&  $this->_data['start_dt'] != '' && $this->_data['start_dt'] == $value ){
            $whereOr[] = $this->dbAdapter->quoteInto("(ti.start_dt < ? AND  ti.end_dt > ? )", HCMS_Utils_Date::dateLocalToIso($this->_data['start_dt']));
        }
        if(isset ($this->_data['end_dt']) &&  $this->_data['end_dt'] != '' && $this->_data['end_dt'] == $value  ){
            $whereOr[] = $this->dbAdapter->quoteInto("(ti.start_dt < ? AND  ti.end_dt > ? )", HCMS_Utils_Date::dateLocalToIso($this->_data['end_dt']));
        }
        if(isset ($this->_data['id']) &&  $this->_data['end_dt'] != ''){
            $whereAnd[] = $this->dbAdapter->quoteInto("ti.id != ? ", $this->_data['id']);
        }
        
        if(isset ($this->_data['end_dt']) &&  $this->_data['end_dt'] != ''  && 
                isset ($this->_data['start_dt']) &&  $this->_data['start_dt'] != ''){
            $whereOr[] = $this->dbAdapter->quoteInto("(ti.start_dt >= ?  AND ", HCMS_Utils_Date::dateLocalToIso($this->_data['start_dt'])).
                        $this->dbAdapter->quoteInto(" ti.end_dt <= ? )", HCMS_Utils_Date::dateLocalToIso($this->_data['end_dt']));
        }
        $whereOr = implode(' OR ', $whereOr);
        $whereAnd = implode(' AND ', $whereAnd);
        $sql = "SELECT COUNT(ti.id) AS doubles
                FROM teaser_item AS ti
                WHERE ($whereOr )AND $whereAnd";

//        echo "\n\n$sql\n\n"; die();
        $resultSet = $this->dbAdapter->fetchAll($sql);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet[0];
        $result = ($row['doubles'] == 0);

        if(!$result){
            $this->_error(self::PERIOD_OCCUPIED);
        }
        return $result;
    }
}