<?php
/**
 * Subscription Mapper
 *
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author milan
 */
class Contact_Model_SubscriptionMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Contact_Model_SubscriptionMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Contact_Model_DbTable_Subscription
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Contact_Model_DbTable_Subscription();
    }

    /**
     * get instance
     *
     *
     * @return Contact_Model_SubscriptionMapper
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
     * @param Contact_Model_Subscription $subscription
     * @return boolean
     */
    public function find($id, Contact_Model_Subscription $subscription) {
        $result = $this->_dbTable->find($id);
        if (0 == count($result)) {
            return false;
        }
        $row = $result->current();
        $subscription->setOptions($row->toArray());
        return true;
    }
    
    /**
     * Find by code and populate entity
     *
     * @param string $id
     * @param string $code
     * @param Contact_Model_Subscription $subscription
     * @return boolean
     */
    public function findByCode($id, $code, Contact_Model_Subscription $subscription) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                  ->from(array('s'=>'contact_subscription'),array('s.*'))
                  ->where('s.code = ?', $code)
                    ->where('s.id = ?', $id);
        
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $subscription->setOptions($row->toArray());
        return true;
    }
    
    /**
     * Find by email and populate entity
     *
     * @param string $email
     * @param Contact_Model_Subscription $subscription
     * @return boolean
     */
    public function findByEmail($email, Contact_Model_Subscription $subscription) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->setIntegrityCheck(false)
                  ->from(array('s'=>'contact_subscription'),array('s.*'))
                  ->where('s.email = ?', $email);
        
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $subscription->setOptions($row->toArray());
        return true;
    }     

    /**
     * Find all Subscriptions
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('s'=>'contact_subscription'),array('s.*'));

        if(isset ($criteria['status'])){
            $select->where('s.status = ?', $criteria['status']);
        }
        if(isset ($criteria['gender'])){
            $select->where('s.gender = ?', $criteria['gender']);
        }
        if(isset ($criteria['lang'])){
            $select->where('s.lang = ?', $criteria['lang']);
        }          
        if(isset ($criteria['subscribed_from'])){
            $select->where('s.subscribed_dt >= ?', $criteria['subscribed_from']);
        }        
        if(isset ($criteria['subscribed_to'])){
            $select->where('s.subscribed_dt < ?', $criteria['subscribed_to']);
        }
        if(isset ($criteria['unsubscribed_from'])){
            $select->where('s.unsubscribed_dt >= ?', $criteria['unsubscribed_from']);
        }        
        if(isset ($criteria['unsubscribed_to'])){
            $select->where('s.unsubscribed_dt < ?', $criteria['unsubscribed_to']);
        }         
        
        if(is_array($orderBy) && count($orderBy) > 0 ){
            $select->order($orderBy);
        }

        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        $subscriptions = array();
        if (0 == count($resultSet)) {
            return $subscriptions;
        }

        foreach ($resultSet as $row) {
            $rowArray = $row->toArray();
            $subscription = new Contact_Model_Subscription();
            $subscription->setOptions($rowArray);

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    /**
     * Save entity
     *
     * @param Contact_Model_Subscription $subscription
     */
    public function save(Contact_Model_Subscription $subscription) {
        $data = array();
        $this->_populateDataArr($data, $subscription, array( 'id','first_name','last_name','email',
                                                            'status','subscribed_dt','unsubscribed_dt',
                                                            'code','gender','lang'
        ));
        $id = $subscription->get_id();
        if (!isset ($id) || $id <= 0) {
            unset($data['id']);
            $subscriptionId = $this->_dbTable->insert($data);
            if($subscriptionId > 0){
                $subscription->set_id($subscriptionId);
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

    /**
     * Delete Page
     *
     * @param int $ids
     */
    public function delete(Contact_Model_Subscription $subscription){
        //delete page
        $result = $this->_dbTable->getAdapter()->delete('contact_subscription', array(
            'id = ?'   => $subscription->get_id()
        ));

        return ($result > 0);
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
                        if($key == 'id' || $key == 'application_id' ){
                            continue;
                        }

                        if(in_array($key, array('posted','subscribed_dt','unsubscribed_dt'))){
                            $objPHPExcel->getActiveSheet()
                                    ->setCellValueByColumnAndRow($col, $row, HCMS_Utils_Time::timeMysql2Local($record[$key]));
                        }
                        else if($key == 'lang'){
                            $objPHPExcel->getActiveSheet()
                                    ->setCellValueByColumnAndRow($col, $row, $languages[$record[$key]]['name']);
                        }
                        else
                        {
                            $objPHPExcel->getActiveSheet()
                                    ->setCellValueByColumnAndRow($col, $row, $record[$key]);
                        }

                        //set size of every column
                        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
                        //apply style to body
                        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->applyFromArray($styleBody);
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


}