<?php
/**
 * Translation Model Mapper
 *
 * @package Translation
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Translation_Model_TranslationMapper
{
    /**
     * singleton instance
     *
     * @var Translation_Model_TranslationMapper
     */
    protected static $_instance;
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_dbTable = null;
    
    /**
     *
     * @var 
     */
    protected $_prefix = '';

    /**
     * Singleton method to create/return instance of class
     *
     * @return Translation_Model_TranslationMapper
     */
    public static function getInstance() {
        if (!isset(self::$_instance) || Zend_Registry::isRegistered('translation_db')) {
            self::$_instance = new Translation_Model_TranslationMapper();
        }
        
        return self::$_instance;
    }

    /**
     * private constructor
     */
    private function  __construct(){
        $this->_dbTable = new Translation_Model_DbTable_Translation();
    }

    /**
     * Get data (new)
     */
    public function getTranslations($page,$rowCount,$parameters, $languageId = array("menu_id", "type_id", "section_id"), $translationLang = array(), $export = false) {
        $response = new stdClass();
        //array of all languages.
        $translationLanguagesArr = $this->getTranslateLanguage();

        /* count total records */
        $translateTotalRecFrom = $this->_dbTable->select()
                                        ->from(array('t' => 'translate'), array('t.id'))
                                        ->group(array('section', 'key'));
        $translateTotalRecords = $this->_dbTable->select()
                                        ->setIntegrityCheck(false)
                                        ->from($translateTotalRecFrom  , array("total" => 'count(*)'));
        
        /* and set limit */
        $translationKeysSelect = $this->_dbTable->select();
        $translationKeysSelect->from(array('t' => 'translate'), array('t.id', 't.key', 't.value', 't.section', 't.language_id'))
                            ->group(array('section', 'key'));
        
                
        if($export == false && isset($parameters['rows']) && isset($parameters['page']))
            $translationKeysSelect ->limit($parameters['rows'], ((int)$parameters['page']-1)* (int)$parameters['rows']);
        
        //search criteria
        if ($parameters['_search'] != 'false') {
            if(isset($parameters['editable']) && $parameters['editable'] == 'true'){
                $translationKeysSelect->where('t.key = ?', $parameters['_search']);
                $translateTotalRecFrom->where('t.key = ?', $parameters['_search']);
            }else{
                $translationKeysSelect->where('t.key LIKE ?', "%$parameters[_search]%");
                $translateTotalRecFrom->where('t.key LIKE ?', "%$parameters[_search]%");
            }
            
        }
        $translationSelect = $this->_dbTable
                                    ->select()
                                    ->setIntegrityCheck(false)
                                    ->from($translationKeysSelect, array('t.id', 't.key', 't.value', 't.section', 't.language_id'));

        $langValueAliasArr = array();
        if (count($translationLanguagesArr) == 0) {
            return array();
        }
        foreach ($translationLanguagesArr as $lang) {
            $langTableAlias = 't_' . $lang['code'];
            $langValueAlias = 'value_' . $lang['code'];
            $langValueAliasArr[] = $langValueAlias;
            $langAlias = $langTableAlias . '.' . $langValueAlias;
            $translationSelect->joinLeft(array($langTableAlias => 'translate'), "`t`.`key` = `" . $langTableAlias . "`.`key` AND
                    `t`.`section` = `" . $langTableAlias . "`.`section`  AND `" . $langTableAlias . "`.`language_id` = " . $lang['id'], array($langValueAlias => "value"));
        }
        $translationSelect->group(array('t.section', 't.key'));
        $translationSelect->order(array("$parameters[sidx] $parameters[sord]"));
        
        $data = $this->_dbTable->fetchAll($translationSelect);
        $totalRecords = $this->_dbTable->fetchRow($translateTotalRecords);
       
        //current page.
        $response->page = $page;
        $response->perPage = $rowCount;
        //total numbers of pages.
        if(isset($parameters['rows']))
            $response->total = ceil($totalRecords['total'] / $parameters['rows']);
        //total number of records.
        $response->records = $totalRecords['total'];
        $i = 0;
        foreach ($data as $val) {
            $response->rows[$i]['id']= htmlspecialchars($val['key']);
            //here is defined static grid data
            $staticColData = array();
            $staticColData = array($val["key"],$val["key"],$val['section']);
            $dynamicColData = array();
            //here is defined dynamic grid data depending of defined number of translation languages
            foreach ($langValueAliasArr as $alias) {
                $dynamicColData[] = $val[$alias];
            }
            // merging static and dynamic data
            $response->rows[$i]['cell'] = array_merge($staticColData, $dynamicColData);
            $i++;
        }
        return $response;
    }
    
    /**
     * save data
     * @param string $key
     * @param int $language_id
     * @param string $val
     * @return boolean
     */
    public function save($key, $language_id, $val, $section  = "", $import = false, $newKey = "") {
        $data = array(
                'key'           => ($newKey == "")?$key:$newKey,
                'language_id'   => $language_id,
                'value'         => $val,
                'section'       => $section
        );
        
        
        $selectTranslate = $this->_dbTable->select()
                                ->where('`language_id` = ?', $language_id)
                                ->where('`key` = ?', $data['key']);
        if($section != "" && !$import){
            $data['section'] = $section; 
        }
        
        if($section != "" && $import){
            $selectTranslate->where('`section` = ?', $section);
        }
        
        $rowTranslate = $selectTranslate->query()->fetchObject(); // ->toArray()
        if(is_object($rowTranslate)){
            $res = $this->_dbTable->update($data, array('id = ?' => $rowTranslate->id));
            if(!$import)
                $this->cacheCleanTranslation();
            return ($res == 1);
        }else{
            $res = $this->_dbTable->insert($data);

            if(!$import)
                $this->cacheCleanTranslation();
            return ($res > 0);
        }
    }

    /**
     * get Translate Language
     * @param int $id
     * @return void
     * 
     */
    public function getTranslateLanguage($id = null){
         $select = $this->_dbTable->select()
                        ->setIntegrityCheck(false)
                        ->from($this->_prefix.'translate_language');
         return $this->_dbTable->fetchAll($select)->toArray();
    }
    
    /**
     * Check if translation exist
     * @param string $key
     * @return bool
     */
    public function getTranslation($key){
        $select = $this->_dbTable->select()->setIntegrityCheck(false)->from($this->_prefix.'translate')->where('`key` = ?', $key);
        $result = $this->_dbTable->fetchAll($select)->count();
        if($result > 0)
            return true;
        else
            return false;
    }
    
    /**
     * Get section 
     * 
     * @return array 
     */
    public function getTranslationSection(&$error = ""){
        $data = array();
        $sectionSelect = $this->_dbTable
                            ->select()
                            ->setIntegrityCheck(false)
                            ->distinct()
                            ->from(array('t'  => $this->_prefix.'translate'), 'section');
        try {
            $result = $this->_dbTable->fetchAll($sectionSelect);
            $data["NULL"] = "";
            foreach ($result as $val){
                $data[$val['section']] = $val['section'];
            }
            return $data;
        } catch (Exception $e) {
            $error = 'Error message: ' . $e->getMessage();
            return $data;
        }
    }
        
    public function importTranslation($fileDestination, $errors){        
        $arrayTemplate= array("Key", "Firstname", "Secondname", "Birthday");
        $inputFileType = PHPExcel_IOFactory::identify($fileDestination);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($fileDestination);
        $total_sheets=$objPHPExcel->getSheetCount(); // here 4
        $allSheetName=$objPHPExcel->getSheetNames(); // array ([0]=>'student',[1]=>'teacher',[2]=>'school',[3]=>'college')
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0); // first sheet
        $highestRow = $objWorksheet->getHighestRow(); // here 5
        $highestColumn = $objWorksheet->getHighestColumn(); // here 'E'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn)-1;  // here 5
        $arr_data = array();
        for ($row = 1; $row <= $highestRow; ++$row) {
            for ($col = 0; $col <= $highestColumnIndex; ++$col) {
                $value=$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                if(is_array($arr_data) ) { $arr_data[$row-1][$col]=$value; }
            }
        }
        $first = true;
        $imported = array();
        $i = 1;
        $translateLanguage  = $this->getTranslateLanguage();
        foreach ($translateLanguage as $key => $value) {
            $langName[$value["code"]] = $value['id'];
        }
        
        foreach ($arr_data as $data) {
            if($first){
                $arrayTemplate  = $data;
                foreach ($data as $key => $val) {
                    $keys[$val] = $key;
                }
            }elseif(!$first){
                $importData = array();
                foreach ($arrayTemplate as $value) {
                    switch ($value) {
                        case "Key":
                            $importData["key"] = $data[$keys["Key"]];
                            break;                        
                        case "Section":                            
                            $importData["section"] = $data[$keys["Section"]];
                            break;
                        default:
                            if(isset($langName[$value])){
                               $importData["lang"][$langName[$value]] = $data[$keys[$value]]; // get language_id from $data[$value]
                            }
                            break;
                    }
                }                
                foreach ($importData["lang"] AS $language_id=>$val){
                    if($val != ""){
                        $this->save($importData["key"], $language_id, $val, $importData["section"], true);  
                    }
                }
                
                
            }
            $i++;
            $first = false;
        }
        $this->cacheCleanTranslation();
        return true; //$imported;
    }
    
    public function exportTranslation($languageId, $grid_params){
        //data array
        $translation = array();

        $fileName = "translation-".Zend_Date::now().".xls";
        /** PHPExcel */
        //require_once 'PHPExcel/PHPExcel.php';
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

        $langName = array();
        $translateLanguage  = $this->getTranslateLanguage();
        
        foreach ($translateLanguage as $key => $value) {
            $langName[$value["id"]] = array("id"=>$value['id'],"code"=>$value['code'], "name"=>$value['name']);
        }
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        //set width of columns
        $columns = array('A','B','C','D','E','F','G', "H", "I");
        foreach ($columns as $value){
            $objPHPExcel->getActiveSheet()->getColumnDimension($value)->setWidth(12);
        }
        //set height of first row
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
        
        //style of header
         $styleHeader = array(
            'font' => array(
                'bold' => true,
//                'color' => array(
//                'argb' => PHPExcel_Style_Color::COLOR_GREEN,
//                ),
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
        //style of report body
        $styleBody = array(
            'font' => array(
                'bold' => false,
//                'color' => array(
//                'argb' => PHPExcel_Style_Color::COLOR_GREEN,
//                ),
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
//                'startcolor' => array(
//                    'argb' => PHPExcel_Style_Color::COLOR_GREEN,
//                ),
            ),
        );

        //header of report
        $activeSheet  = $objPHPExcel->getActiveSheet();
        $activeSheet  ->setCellValue('A1', 'Key');
        $activeSheet  ->setCellValue('B1', 'Section');
        $a = 0;
        $abc = array("C", "D", "E", "F", "G", "H", "I");
        
        
        $translationLang =array();
        foreach ($languageId AS $lang){
            if(((int)$lang)){
                $translationLang[] = $langName[$lang];
                $activeSheet -> setCellValue($abc[$a]."1", $langName[$lang]['code']);
                $a++;
            }
        }
        
        //apply style to header
        $activeSheet->getStyle('A1:'.$abc[$a-1].'1')->applyFromArray($styleHeader);
        //data from db
        $i =2;
        
        //get data for specific $languageId
        $translation = $this->getAllTranslation($languageId, $translationLang, $grid_params);
        
        foreach ($translation as $transl) {
            $s = 2;
            $activeSheet ->setCellValueByColumnAndRow(0 , $i,  $transl[0]);
            $activeSheet ->setCellValueByColumnAndRow(1 , $i,  $transl[2]);
            foreach($languageId AS $lang){
                 $activeSheet ->setCellValueByColumnAndRow($s , $i,  $transl[$s+1]);
                 $s++;
            }
            //apply style to row
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$abc[$a-1].$i)->applyFromArray($styleBody);
            // increment counter
            $i++;          
        }
        //------------- Buffering ----------//
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Translation');

        // redirect output to client browser
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');   

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //
        //save to file
        //$objWriter->save($file);
        // output to browser
        $objWriter->save('php://output');
        return true;
    }
    
    /**
     * Get All translation for export
     * @param type $languageId
     * @param type $translationLang
     * @param type $parameters
     * @return type 
     * 
     */
    public function getAllTranslation($languageId , $translationLang, $parameters){
        if($parameters["_search"] == '1') $parameters["_search"] = ""; else $parameters["_search"]= "false";
        
        Translation_Model_Util::prepareDataParams($parameters);
        $data  = $this->getTranslations(1, "", $parameters, $languageId, $translationLang, true);
        $translation =  array();
        foreach ($data->rows as $row){
            $translation[] = $row['cell'];
        }
        return $translation; 
    }
    
    /**
     * Delete data
     *
     * @param int $id
     * @return int|bool
     */
    public function delete($del_id){
//        $this->_dbTable->select()
//                ->setIntegrityCheck(false)
//                ->from($this->_prefix.'translate_menu'); 
        $this->_dbTable->getAdapter()->delete( $this->_prefix.'translate',array('`key` = ?' => $del_id));
        return $this->_dbTable->getAdapter()->delete( $this->_prefix.'translate_key',array('`key` = ?' => $del_id));
    }
    
    /**
     * Clean cache translation 
     * 
     */
    public function cacheCleanTranslation(){
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $cacheClearUrl = $request->getScheme() . '://' . $request->getHttpHost().'/'.CURR_LANG.'/default/lang-js/cache-clean-translation' ;
        if($cacheClearUrl != ""){
            $client = new Zend_Http_Client();
            $client ->setUri($cacheClearUrl)
                    ->setConfig(array(
                                    'maxredirects' => 2,
                                    'timeout'      => 30))
                    ->request('GET');
        }
    }
    
  
    /**
     * Get options
     *
     * @return array
     */
    public function getOptions($view) {
        $colNames = array();
        $colNames[0] = "Key";
        $colNames[1] = "Key";
        $colNames[2] = "Section";
        $translationLang = $this->getTranslateLanguage();
        
        $i = 2 ;
        foreach($translationLang as $colName ){
            $i++;
            $colNames[$i] = $colName["name"];
        }

        return array('colNames' => $colNames);
    }
    
}