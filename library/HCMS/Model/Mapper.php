<?php
/**
 * Base Maper model.
 *
 * @package HCMS
 * @subpackage Model
 * @copyright Horisen
 * @author milan
 */
class HCMS_Model_Mapper {
    /**
     * @var Zend_Log
     */
    protected $_logger = null;

    /**
     *
     * @var HCMS_Model_Table
     */
    protected $_dbTable;

    protected function _initLogger(){
        if (Zend_Registry::isRegistered('Zend_Log')) {
            $this->_logger = Zend_Registry::get('Zend_Log');
        }
    }

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @param  mixed    $extras    Extra information to log in event
     * @return void
     * @throws Zend_Log_Exception
     */
    protected function _log($message, $priority, $extras = null){
        if(isset ($this->_logger)){
            $this->_logger->log($message, $priority, $extras);
        }
    }

    protected function _getJsonData($json){
        return (isset ($json) && $json != '')?json_decode($json, true):array();
    }
    
    protected function _arrayToJson($array,$key){
        return (isset ($array[$key]) && is_array($array[$key]))?json_encode($array[$key]):'';
    }

    /**
     * Get Paging rows
     * 
     * @param array $paging
     * @param Zend_Db_Select $select
     *
     * return array
     */
    protected function _getPagingRows(&$paging,Zend_Db_Select $select){
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($paging['perPage'])
                  ->setCurrentPageNumber($paging['page']);

        $paging['page'] = $paginator->getCurrentPageNumber();
        $paging['perPage'] = $paginator->getItemCountPerPage();
        $paging['total'] = $paginator->count();
        $paging['records'] = $paginator->getTotalItemCount();
        $paging['paginator'] = $paginator;

        return $paginator;
    }

    /**
     * Populate array key from entity field
     *
     * @param array $data
     * @param BZ_Model_Entity $object
     * @param string|array $fieldName
     * @param array $jsonFields
     */
    protected function _populateDataArr(&$data, $object, $fieldName, $jsonFields = array()){
        if(is_array($fieldName)){
            foreach ($fieldName as $field) {
                $this->_populateDataArr($data, $object, $field, $jsonFields);
            }
        }
        else{
            if(null != $object->$fieldName){
                if(!in_array($fieldName, $jsonFields)){
                    $data[$fieldName] = $object->$fieldName;
                }
                else{                    
                    $data[$fieldName] = (is_array($object->$fieldName))?json_encode($object->$fieldName):'';
                }
            }
        }
    }

    /**
     * Join SELECT to translation table
     *
     * @param string $language
     * @param Zend_Db_Select $select
     * @param string $tableName
     * @param string $tableAlias
     * @param string $tableKey
     * @param array $fields
     */
    protected function _makeTranslationJoin($language, Zend_Db_Select $select, $tableName, $tableAlias, $tableKey, $fields){
        $trTableName = $tableName . '_tr';
        $trTableAlias = $tableAlias . 't';
        $selectFields = array();
        foreach ($fields as $field) {
            $fieldTr = $field . "_tr";
            $selectFields[$fieldTr] = "$trTableAlias.$field";
        }
        $select->joinLeft(
            array($trTableAlias => $trTableName),
            $select->getAdapter()->quoteInto("$trTableAlias.translation_id = $tableAlias.$tableKey AND $trTableAlias.language = ?", $language),
            $selectFields
        );
    }

    /**
     * Change regular field with translations - if exists
     *
     * @param array $data
     * @param array $fields
     * @param array $jsonFields
     */
    protected function _makeTranslationData(&$data, $fields, $jsonFields = array()){
        foreach ($fields as $field) {
            $fieldTr = $field . "_tr";
            if(isset ($data[$fieldTr]) && $data[$fieldTr] != ''){
                $data[$field] = $data[$fieldTr];
            }
        }
        foreach ($jsonFields as $jsonField) {
            $data[$jsonField] = $this->_getJsonData($data[$jsonField]);
        }
        return $data;
    }

    /**
     * Save data in _tr table
     * 
     * @param string $tableName
     * @param int $entityId
     * @param HCMS_Model_Entity $object
     * @param array $fields
     * @param string $language
     * @param array $jsonFields
     * @return int
     */
    protected function _saveTranslation($tableName, $entityId, HCMS_Model_Entity $object, $fields, $language, $jsonFields = array()){
        $trTableName = $tableName . "_tr";
        $data = array();
        $this->_populateDataArr($data, $object, $fields, $jsonFields);
        $select = $this->_dbTable->getAdapter()->select();
        $select ->from($trTableName)
                ->where("translation_id = ?",$entityId)
                ->where("language = ?",$language);
        $resultSet = $this->_dbTable->getAdapter()->fetchAll($select);
        if (0 == count($resultSet)) {
            $data['translation_id'] = $entityId;
            $data['language'] = $language;
            $id = $this->_dbTable->getAdapter()->insert($trTableName,$data);
            return 1;
        }
        else{
            return $this->_dbTable->getAdapter()->update($trTableName, $data,
                    array(
                        'translation_id = ?' => $entityId,
                        'language = ?'      => $language
                    ));
        }

    }

    /**
     * Parse interval string and return array: interval => , range =>
     * @param string $interval
     * @return array
     */
    protected function _parseInterval($interval){
        $period = explode('_', $interval);
        if($period[0] == 'month'){
            $interval = 'MONTH';
        }
        else{
            $interval = 'YEAR';
        }
        $range = (int)$period[1];
        return array(
            'interval'  => $interval,
            'range'     => $range
        );
    }
}
