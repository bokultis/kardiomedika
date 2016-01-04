<?php
/**
 * Sitemap importer - from xls
 *
 * @package Cms
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */

class Cms_Model_SiteMapImporter extends HCMS_Model_Mapper
{
    
    protected $reader;
    protected $excel;
    protected $sheet;
    protected $menuCode;
    protected $columns;
    protected $data;
    protected $languages = array();
    protected $modules = array(
        'placeholder'   => array(
            'path'  => null,
            'route' => ''
        ),
        'external'   => array(
            'path'  => null,
            'route' => ''
        ),         
        'cms'   => array(
            'path'  => 'cms/page/index',
            'route' => 'cms'
        ),
        'cms/home'   => array(
            'path'  => 'cms/page/index',
            'route' => 'cms',
            'route_uri' => ''
        ),        
        'cms/sitemap'   => array(
            'path'  => 'cms/sitemap/index',
            'route' => 'cms'
        ),
        'contact'   => array(
            'path'  => 'contact/generic/index',
            'params'=> 'form_id/contact',
            'route' => 'cms'
        ),            
    );
    protected $errors = array();
    /**
     *
     * @var Zend_Filter_Interface 
     */
    protected $seoFilter;
    
    public function importXls($fileDestination)
    {
        $this->createSeoFilter();
        $this->loadLanguages();
        try {
            $this->reader = PHPExcel_IOFactory::createReader(PHPExcel_IOFactory::identify($fileDestination));
            $this->reader->setReadDataOnly(true);
            $this->excel = $this->reader->load($fileDestination);
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $this->errors['ERR_IMPORT_XLS'] = 'Error reading the file, incorrect format';
            return false;
        }        
        $this->truncateData();
        return $this->importSheets();
    }
    
    
    protected function createSeoFilter()
    {
        $this->seoFilter = HCMS_Filter_CharConvert::createSEOFilter();
    }
    
    protected function importSheets()
    {        
        //$totalSheets=$this->excel->getSheetCount();
        $menusArr = Cms_Model_MenuMapper::getInstance()->getMenus();
        $menus = array();
        foreach ($menusArr as $currMenu) {
            $menus[] = $currMenu['code'];
        }        
        $sheetToMenu = array();
        $sheets = $this->excel->getSheetNames();
        if(count($sheets) == 1){
            $sheetToMenu[0] = $menus[0];
        } else {
            foreach ($sheets as $sheetIndex => $sheetName){
                if(!in_array(strtolower($sheetName), $menus)){
                    $this->errors['ERR_SHEET_NAME'] = 'Menu not found for defined sheet';
                    return false;
                }
                $sheetToMenu[$sheetIndex] = strtolower($sheetName);
            }            
        }       
        foreach ($sheets as $sheetIndex => $sheetName){   
            $this->sheet = $this->excel->setActiveSheetIndex($sheetIndex);
            $this->loadColumns();
            $this->loadData();                    
            $this->saveData($sheetToMenu[$sheetIndex]);            
        }
        return true;
    }    
    
    protected function loadColumns()
    {
        $highestColumn = $this->sheet->getHighestColumn(); // here 'E'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn)-1;  // here 5
        $this->columns = array();
        //read columns from 1st row
        for ($col = 0; $col <= $highestColumnIndex; $col++) {
            $value = $this->getCellValue($col, 0);
            //echo "\n$col, 0: " . $value . "\n";
            if(!$value || $value == ''){
                continue;
            }
            $this->columns[$value] = $col;
        }
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    
    protected function getCellValue($column, $row)
    {
        return trim($this->sheet->getCellByColumnAndRow($column, $row + 1)->getValue());
    }
    
    protected function getRowLevel($row)
    {
        for($currentLevel = 1; $currentLevel < 20; $currentLevel++){
            if(isset($this->data[$row]['Level ' . $currentLevel]) && $this->data[$row]['Level ' . $currentLevel]){
                return $currentLevel;
            }
        }
        return null;
    }
    
    protected function findParentRow($row)
    {
        $currentLevel = $this->getRowLevel($row);
        if(!$currentLevel || $currentLevel == 1){
            return false;
        }                
        while($row >= 0){
            $row--;
            $candidateLevel = $this->getRowLevel($row);
            //echo "\nSearching parent row in: $row with level: " . ($currentLevel - 1) . " and level found: $candidateLevel \n";
            if($candidateLevel == ($currentLevel - 1)){
                //echo "\n found row $row\n";
                return $row;
            }
        }
        return false;
    }
    
    protected function truncateData()
    {        
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $dbAdapter->query('TRUNCATE cms_menu_item');
        $dbAdapter->query('TRUNCATE cms_menu_item_tr');
        $dbAdapter->query('TRUNCATE cms_page');
        $dbAdapter->query('TRUNCATE cms_page_tr');
        $dbAdapter->query('TRUNCATE cms_route');
    }
    
    protected function loadData()
    {
        $highestRow = $this->sheet->getHighestRow();
        $this->data = array();
        $row = -1;
        for ($xlsRow = 0; $xlsRow <= $highestRow; $xlsRow++) {
            $row++;
            //skip header - row + 1
            foreach ($this->columns as $colName => $colIndex) {
                $this->data[$row][$colName] = $this->getCellValue($colIndex, $xlsRow + 1);
            }
            $currentLevel = $this->getRowLevel($row);
            if(!$currentLevel){
                unset($this->data[$row]);
                $row--;
                continue;                
            }            
            $this->data[$row]['level'] = $currentLevel;
            $this->data[$row]['Name'] = $this->data[$row]['Level ' . $currentLevel];
            $this->data[$row]['parent'] = null;
            $parentRow = $this->findParentRow($row);            
            if($parentRow !== false){
                $this->data[$row]['parent'] = $parentRow;
                if(!isset($this->data[$parentRow]['items'])){
                    $this->data[$parentRow]['items'] = array();
                }
                $this->data[$parentRow]['items'][] = $this->data[$row];                
            }
            if(!isset($this->data[$row]['Module']) || !isset($this->modules[$this->data[$row]['Module']])){
                $this->data[$row]['Module'] = 'cms';
            }            
        }
    }
    
    protected function saveData($menuCode)
    {
        foreach ($this->data as $row => $values) {       
            if($values['Module'] == 'cms' || $values['Module'] == 'cms/home'){
                $this->savePage($row);
            }
            $this->saveMenuItem($row, $menuCode);
            $this->saveRoute($row);
        }
    }
    
    protected function loadLanguages()
    {
        $transMapper = Application_Model_TranslateMapper::getInstance();
        $defLanguage = $transMapper->getDefaultLang();
        $languages = $transMapper->getLanguages();
        $this->languages[] = $defLanguage;
        foreach ($languages as $code => $lang) {
            if(!$lang['default'] && $lang['front_enabled']){
                $this->languages[] = $code;
            }
        }
    }
    
    /**
     * Get localized version from texts
     * 
     * @param array $texts
     * @param int $langIndex
     * @return string
     */
    protected function getLangText(array $texts, $langIndex)
    {
        return isset($texts[$langIndex]) ? $texts[$langIndex]: $texts[0];
    }
    
    /*
     * Get localized version from meta keywords
     * 
     * @param array $keywords
     * @param int $langIndex
     * @return string
     */
    protected function getMetaKeywords(array $keywords, $langIndex){
        return isset($keywords[$langIndex]) ? $keywords[$langIndex] : $keywords[0];
    }
    
    /*
     * Get localized version from meta descriptions
     * 
     * @param array $descriptions
     * @param int $langIndex
     * @return string
     */
    protected function getMetaDescription(array $descriptions, $langIndex){
        return isset($descriptions[$langIndex]) ? $descriptions[$langIndex] : $descriptions[0];
    }
    
    protected function savePage($row)
    {
        $titles = explode("\n", $this->data[$row]['Name']);
        if(isset($this->data[$row]['Meta Keywords'])){
            $metaKeywords = explode("\n", $this->data[$row]['Meta Keywords']);
            
        }
        if(isset($this->data[$row]['Meta Description'])){
            $metaDescriptions = explode("\n", $this->data[$row]['Meta Description']);      
        }
        
        
        $page = new Cms_Model_Page();
        $page   ->set_application_id(1)
                ->set_format('html')
                ->set_status('published')
                ->set_type_id(1)
                ->set_user_id(1)
                ->set_posted(HCMS_Utils_Time::timeTs2Mysql(time()));
        
        foreach ($this->languages as $langIndex => $lang) {
            $curRow = $row;
            $title = $this->getLangText($titles, $langIndex);
            if(isset($metaKeywords)){
                $metaKeyword = $this->getMetaKeywords($metaKeywords, $langIndex);
            }
            if(isset($metaDescriptions)){
                $metaDescription = $this->getMetaDescription($metaDescriptions, $langIndex);
            }

            $urlIdParts = array();
                while(isset($this->data[$curRow])){
                    $currTitles = explode("\n", $this->data[$curRow]['Name']);
                    if(isset($metaKeyword) && isset($metaDescription)){
                        $meta = array("keywords" => $metaKeyword, 
                                      "description" => $metaDescription);                        
                    }else{
                        $meta = array("keywords" => "", "description" => "");                        
                    }
                    if(isset($currTitles[$langIndex])){
                        $urlIdParts[] = $this->seoFilter->filter($currTitles[$langIndex]);
                    }
                    
                    if(!isset($this->data[$curRow]['parent'])){
                        break;
                    }
                    $curRow = $this->data[$curRow]['parent'];
                }
            $urlIdParts = array_reverse($urlIdParts);
            $urlId = implode('-', $urlIdParts);
            
            $page   ->set_code('')
                    ->set_url_id($urlId)
                    ->set_content('<h1>' . $title . '</h1>')
                    ->set_title($title)
                    ->set_meta($meta);

            if(Cms_Model_PageMapper::getInstance()->findByUrlId($urlId, 1, $page)){
                continue;
            }
            Cms_Model_PageMapper::getInstance()->save($page, $lang);
        }
        $this->data[$row]['page_id'] = $page->get_id();          
    }    
    
    protected function saveMenuItem($row, $menuCode)
    {
        $module = $this->modules[$this->data[$row]['Module']];
        $titles = explode("\n", $this->data[$row]['Name']);
        $item = new Cms_Model_MenuItem();
        $item   ->set_application_id(1)
                ->set_menu($this->menuCode)
                ->set_route($module['route'])
                ->set_menu($menuCode);
        //set page id
        if(isset($this->data[$row]['page_id'])){
            $item->set_page_id_new($this->data[$row]['page_id']);
        }
        //set path / params        
        $item->set_path($module['path']);
        if(isset($module['params'])){
            $item->set_params($module['params']);
        } else {
            $item->set_params("");
        }
        $item->set_params_old("");
        //uri
        if($this->data[$row]['Module'] == 'external' && isset($this->data[$row]['External url'])){
            $item->set_uri($this->data[$row]['External url']);
        }
        //set order
        $item->set_ord_num($row);                
        foreach ($this->languages as $langIndex => $lang) {
            $title = $this->getLangText($titles, $langIndex);
            $item   ->set_name($title)
                    ->set_level($this->data[$row]['level'])
                    ->set_parent_id(0);
            //set parent
            if(isset($this->data[$row]['parent'])){
                $parentRow = $this->data[$this->data[$row]['parent']];
                if(isset($parentRow['menu_item_id'])){
                    $item->set_parent_id($parentRow['menu_item_id']);
                }
            }
            
            Cms_Model_MenuItemMapper::getInstance()->save($item, $lang, true);
        }
        $this->data[$row]['menu_item_id'] = $item->get_id();          
    }   
    
    protected function saveRoute($row)
    {
        $module = $this->modules[$this->data[$row]['Module']];
        //no path - no route
        if(!$module['path']){
            return;
        }
        $titles = explode("\n", $this->data[$row]['Name']);
        $route = new Cms_Model_Route();
        $route   ->set_application_id(1);
        if(isset($this->data[$row]['page_id'])){
            $route->set_page_id($this->data[$row]['page_id']);
        }        
        $route->set_path($module['path']);
        if(isset($module['params'])){
            $route->set_params($module['params']);
        }         
        foreach ($this->languages as $langIndex => $lang) {
            $title = $this->getLangText($titles, $langIndex);
            $currRow = $row;
            if(!isset($module['route_uri'])){
                $uriParts = array();
                while(isset($this->data[$currRow])){
                    $currTitles = explode("\n", $this->data[$currRow]['Name']);
                    $uriParts[] = $this->seoFilter->filter($currTitles[$langIndex]);
                    if(!isset($this->data[$currRow]['parent'])){
                        break;
                    }
                    $currRow = $this->data[$currRow]['parent'];
                }
                //print_r($uriParts);
                $uriParts = array_reverse($uriParts);
                $uri = implode('/', $uriParts);
            } else {
                $uri = $module['route_uri'];
            }
            
            //check if route exists
            $existingRoutes = Cms_Model_RouteMapper::getInstance()->fetchAll(array('uri' => $uri, 'lang' => $lang));
            if(count($existingRoutes) > 0){
                continue;
            }            
            $route  ->set_name($title)
                    ->set_lang($lang)
                    ->set_uri($uri); 
            $route->set_id(null);
            //print_r($route);
            Cms_Model_RouteMapper::getInstance()->save($route);
        }
        $this->data[$row]['route_id'] = $route->get_id();          
    }    
}
