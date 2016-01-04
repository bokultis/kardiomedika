<?php
/**
 * Contact Mapper
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Model_ContactMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Contact_Model_ContactMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Contact_Model_DbTable_Contact
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Contact_Model_DbTable_Contact();
    }

    /**
     * get instance
     *
     *
     * @return Contact_Model_ContactMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Contact_Model_Contact $contact
     * @return boolean
     */
    public function find($id, Contact_Model_Contact $contact) {
        $result = $this->_dbTable->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $contact->setOptions($row->toArray());
        return true;
    }

    /**
     * Find all contacts
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('c'=>'contact'),array('c.*'));

        if(isset ($criteria['application_id'])){
            $select->where('c.application_id = ?', $criteria['application_id']);
        }
        
        if(isset ($criteria['form_id'])){
            $select->where('c.form_id = ?', $criteria['form_id']);
        }

        if(isset ($criteria['lang_filter'])){
            $select->where('c.language = ?', $criteria['lang_filter']);
        }

        if(isset ($criteria['from_filter'])){
            $select->where('date(c.posted) >= ?', $criteria['from_filter']);
        }

        if(isset ($criteria['to_filter'])){
            $select->where('date(c.posted) <= ?', $criteria['to_filter']);
        }

        if(isset ($criteria['search_filter'])){
            $search = new Zend_Db_Expr($select->getAdapter()->quoteInto("?", $criteria['search_filter']."%"));            
            $select->where("(c.first_name LIKE $search OR
                             c.last_name LIKE $search OR
                             c.email LIKE  $search OR
                             c.gender LIKE $search OR
                             c.phone LIKE $search OR
                             c.mobile LIKE $search  OR
                             c.fax LIKE $search OR
                             c.country LIKE $search OR
                             c.zip LIKE $search OR
                             c.city LIKE $search OR
                             c.street LIKE $search OR
                             c.description LIKE $search)"
                          );
        }
        
        if(is_array($orderBy) && count($orderBy) > 0 ){
            $select->order($orderBy);
        }

        ;
        if(isset($criteria['data_type']) && $criteria['data_type'] == 'array'){
            $resultSet = $this->_dbTable->fetchAll($select);
            return $resultSet->toArray();
        }
        
        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        $contacts = array();
        if (0 == count($resultSet)) {
            return $contacts;
        }

        foreach ($resultSet as $row) {
            $rowArray = $row->toArray();
            $contact = new Contact_Model_Contact();
            $contact->setOptions($rowArray);

            $contacts[] = $contact;
        }

        return $contacts;
    }

    /**
     * Save entity
     *
     * @param Contact_Model_Contact $contact
     */
    public function save(Contact_Model_Contact $contact) {
        $data = array();
        $this->_populateDataArr($data, $contact, array( 'id','application_id','gender','posted','first_name',
                                                        'last_name','email','zip','street','city','country','phone','mobile','fax','subject',
                                                        'description','language', 'message', 'company', 'fileupload', 'form_id'
        ));
        $id = $contact->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $contactId = $this->_dbTable->insert($data);
            if($contactId > 0){
                $contact->set_id($contactId);
                return true;
            }
            else{
                return false;
            }
        } else {
            $result  = $this->_dbTable->update($data, array('id = ?' => $id));
            return $result > 0;
        }
    }


    public function exportToExcel($applicationId, $headerData, $records, $controller) {
        $logger = Zend_Registry::get('Zend_Log');
        try {
            /** PHPExcel */
            //require_once APPLICATION_PATH . '/../library/PHPExcel/PHPExcel.php';
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            // Set properties
            $objPHPExcel->getProperties()->setCreator("Horisen")
                    ->setLastModifiedBy("Horisen")
                    ->setTitle("Office  XLS Test Document")
                    ->setSubject("Office  XLS Test Document")
                    ->setDescription("Test document for Office XLS, generated using PHP classes.")
                    ->setKeywords("office 5 openxml php")
                    ->setCategory("Test result file");

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            //------------- HEADER settings ------------//
            //define style
            $styleHeader = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'argb' => 'FFA0A0A0',
                    ),
                ),
            );

            $languages = Application_Model_TranslateMapper::getInstance()->getLanguages();

            //set size of every column
            $columns = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
            foreach ($columns as $value) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setAutoSize(true);
            }
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);

            //fill with data
            $activeSheet = $objPHPExcel->getActiveSheet();
            $i = 0;
            foreach ($headerData as $key => $val) {
                    $activeSheet->setCellValue($columns[$i] . "1", $controller->translate($val));
                    //apply style to header
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($i, "1")->applyFromArray($styleHeader);
                    $i++;
            }

            //------------- BODY settings ------------//
            //define style
            $styleBody = array(
                'font' => array(
                    'bold' => false,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                ),
            );

            if (count($records) > 0) {
                //fill with data
                $row = 2;
                foreach ($records as $record) {
                    $col = 0;
                    foreach ($headerData as $key => $value) {
                        if(isset($record[$key])){
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $record[$key]);
                            //set size of every column
                            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
                        }
                        $col++;
                    }
                    $row++;
                }
            }
            return $objPHPExcel;
        } catch (Exception $e) {
            $logger->log($e->getMessage(), Zend_Log::CRIT);
            $logger->log("Exception in export to excel!", Zend_Log::CRIT);
            return false;
        }
    }

    /**
     * Delete Page
     *
     * @param int $ids
     */
    public function delete(Contact_Model_Contact $contact){
        //delete page
        $result = $this->_dbTable->getAdapter()->delete('contact', array(
            'id = ?'   => $contact->get_id()
        ));

        return ($result > 0);
    }


}