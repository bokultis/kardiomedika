<?php

/**
 * Admin Teaser controller
 *
 * @package Teaser
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Teaser_AdminTeaserController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxDialogForm
     */
    protected $_formHelper = null;
    protected $_defaultLang = null;

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxDialogForm');
        $this->_defaultLang = Application_Model_TranslateMapper::getInstance()->getDefaultLang();
    }

    /**
     * List Teaser
     */
    public function indexAction() {
        /* Resolves the default format from Zend Locale to a jQuery Date Picker */
        $this->view->picker = HCMS_Utils_Date::resolveZendLocaleToDatePickerFormat();
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->defaultLang = $this->_defaultLang;
    }

    /**
     * Ajax listing of Teasers
     */
    public function listAction() {
        //criteria
        $criteria = array();
        if (null != $this->_getParam('name')) {
            $criteria['name'] = $this->_getParam('name');
        }
        if (null != $this->_getParam('start_dt')) {
            $criteria['start_dt'] = $this->_getParam('start_dt');
        }
        if (null != $this->_getParam('end_dt')) {
            $criteria['end_dt'] = $this->_getParam('end_dt');
        }
        if (null != $this->_getParam('langFilter')) {
            $criteria['lang'] = $this->_getParam('langFilter');
        }
        if (null != $this->_getParam('box_code')) {
            $criteria['box_code'] = $this->_getParam('box_code');
        }
        if (null != $this->_getParam('menu_item_id')) {
            $criteria['menu_item_id'] = $this->_getParam('menu_item_id');
        }        
        //order
        $order = $this->_request->getParam('order');
        if ($order) {
            $order = array($order);
        } else {
            $order = array('t.name ASC', 'thi.order_num ASC');
        }
        //paging
        $page = $this->_request->getParam('page');
        $perPage = $this->_request->getParam('perPage');
        if(!isset ($page) || $page < 1){
            $page = 1;
        }
        if(!isset ($perPage) || $perPage < 1 || $perPage > 300){
            $perPage = 10;
        }
        $paging = array(
            'page'      => $page,
            'perPage'   => $perPage
        );
        $boxes = Teaser_Model_TeaserMapper::getInstance()->getBox();
        $records = Teaser_Model_TeaserMapper::getInstance()->fetchWithItems($criteria, $order, $paging);
        $data = array(
            'total'     => $paging['total'],
            'page'      => $paging['page'],
            'records'   => $paging['records'],
            'perPage'   => $paging['perPage'],
            'rows'      => array()
        );
        /* @var $record Teaser_Model_Teaser */
        foreach ($records as $record) {
            $teaser = array(
                'id'        => $record->get_id(),
                'box_code'  => $record->get_box_code(),
                'box_name'  => isset($boxes[$record->get_box_code()]['name'])? $boxes[$record->get_box_code()]['name']: '',
                'name'      => $record->get_name(),
                'items'     => array()
            );
            $items = $record->get_items();
            /* @var $item Teaser_Model_Item */
            foreach ($items as $item) {
                $status = $this->_getItemStatus($item);
                $teaser['items'][] = array(
                    'id'        => $item->get_id(),
                    'fallback'  => $item->get_fallback(),
                    'start_dt'  => HCMS_Utils_Date::dateIsoToLocal($item->get_start_dt(), "HH:mm"),
                    'end_dt'    => HCMS_Utils_Date::dateIsoToLocal($item->get_end_dt(), "HH:mm"),
                    'title'     => $item->get_title(),
                    'content_type' => $item->get_content_type(),
                    'content'   => $item->get_content(),
                    'box_code'  => $item->get_box_code(),
                    'order_num' => $item->get_order_num(),
                    'status'    => $status,
                    'status_name'=> $this->view->translate($status)
                );                
            }
            $data['rows'][] = $teaser;
        }

        $this->_helper->json->sendJson($data);
    }
    
    /**
     * Get item status
     * 
     * @param Teaser_Model_Item $item
     * @return 'pending'|'active'|'expired' 
     */
    protected function _getItemStatus(Teaser_Model_Item $item){
        $now = Zend_Date::now();
        $start = new Zend_Date($item->get_start_dt(), Zend_Date::ISO_8601);
        $end = new Zend_Date($item->get_end_dt(), Zend_Date::ISO_8601);
        if($item->get_fallback() == 'yes'){
            return 'fallback';
        }        
        else if($start->isLater($now)){
            return 'pending';
        }
        else if($end->isEarlier($now)){
            return 'expired';
        }
        else{
            return 'active';
        }
    }

    public function editAction(){
        $data = $this->getRequest()->getPost('data');
        $id = $this->_getParam('id');
        $cloneId = $this->_getParam('clone_id');

        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }        

        //create form object
        $form = new Teaser_Form_Teaser($data);

        //postback - save?
        if ($this->_formHelper->isSave()) { 
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();
                //create entity object from submitted values, and save
                $teaser = new Teaser_Model_Teaser($values);
                Teaser_Model_TeaserMapper::getInstance()->save($teaser);             
                
                if($cloneId){                    
                    $old_teaser = new Teaser_Model_Teaser();
                    Teaser_Model_TeaserMapper::getInstance()->find($cloneId, $old_teaser);                            
                    Teaser_Model_TeaserMapper::getInstance()->populateItem($old_teaser);
                                    
                    foreach ($old_teaser->get_items() as $item) {                        
                        $item->set_teaser_ids(array('0' => $teaser->get_id()));
                        $item->set_id('');
                        Teaser_Model_ItemMapper::getInstance()->save($item, null);
                    }
                }               
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'index')), $this->translate('Teaser saved.'));
            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        //first run of the form - grab data from mapper
        elseif(!$this->_formHelper->getRequest()->isPost()) {
            if(!isset($id) && isset($cloneId)){
                $id = $cloneId;
            }
              
            //edit action
            if(isset ($id) && $id > 0) {
                $teaser = new Teaser_Model_Teaser();
                if(!Teaser_Model_TeaserMapper::getInstance()->find($id, $teaser)){
                    throw new Exception("Teaser not found");
                }
                Teaser_Model_TeaserMapper::getInstance()->populateMenuItemIds($teaser);

                //fetch data
                $data = $teaser->toArray();
                $data['menu_item_ids'] = $teaser->get_menu_item_ids();
                if(isset($cloneId)){
                    unset($data['id']);
                    $data['name'] = 'Clone ' . $data['name'];
                }
                //populate form with data
                $form->setData($data);
            }
            if(null !=  $this->_getParam('box_code')){
                $data['box_code'] = $this->_getParam('box_code');
            }
        }
        if(!isset($data['menu_item_ids'])){
            $data['menu_item_ids'] = array();
        } 

        $this->view->data = $data;
    }    
    
    public function deleteAction(){
        $id = $this->_getParam('id');
        $teaser = new Teaser_Model_Teaser();
        if(!Teaser_Model_TeaserMapper::getInstance()->find($id, $teaser)){
            $result = array(
                "success" => false,
                "message" => $this->translate("Teaser not found.")
            );
        }else{
            $success = Teaser_Model_TeaserMapper::getInstance()->delete($teaser);
            $result = array(
                        "success" => $success,
                        "message" => ($success)?$this->translate("Teaser deleted."):$this->translate("Error deleting Teaser.")
                    );
        }
        return $this->_helper->json($result);
    }
    
    public function itemEditAction(){
        $data = $this->getRequest()->getPost('data');
        $id = $this->_getParam('id');
        $cloneId = $this->_getParam('clone_id');
        $langFilter = $this->_getParam('langFilter');
        $defaultTeaserId = $this->_getParam('teaser_id');

        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }        

        //create form object
        $form = new Teaser_Form_TeaserItem($data);
        
        //postback - save?
        if ($this->_formHelper->isSave()) {             
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();                
                //create entity object from submitted values, and save
                $item = new Teaser_Model_Item($values); 
                $item->set_start_dt(HCMS_Utils_Date::dateLocalToIso($item->get_start_dt()));
                if(isset($data["end_dt"]) && $data["end_dt"] != ""){
                    $item->set_end_dt(HCMS_Utils_Date::dateLocalToIso($item->get_end_dt()));
                }
                Teaser_Model_ItemMapper::getInstance()->save($item, ($langFilter != '')?$langFilter:null);                
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'index')), $this->translate('Item saved.'));
            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        //first run of the form - grab data from mapper
        elseif(!$this->_formHelper->getRequest()->isPost()) {
            if(!isset($id) && isset($cloneId)){
                $id = $cloneId;
            }
            //edit action
            if(isset ($id) && $id > 0) {
                $item = new Teaser_Model_Item();
                if(!Teaser_Model_ItemMapper::getInstance()->find($id, $item, ($langFilter != '')?$langFilter:null)){
                    throw new Exception("Item not found");
                }
                Teaser_Model_ItemMapper::getInstance()->populateTeaserIds($item);
                //fetch data
                $data = $item->toArray();
                $data['start_dt'] = HCMS_Utils_Date::dateIsoToLocal($item->get_start_dt(),  "HH:mm");
                $data['end_dt'] = HCMS_Utils_Date::dateIsoToLocal($item->get_end_dt(),  "HH:mm");
                $data['teaser_ids'] = $item->get_teaser_ids();
                if(isset($cloneId)){
                    unset($data['id']);
                    $data['title'] = 'Clone ' . $data['title'];
                }
                //populate form with data
                $form->setData($data);
            }
            if(null !=  $this->_getParam('box_code')){
                $data['box_code'] = $this->_getParam('box_code');
            }            
        }
        $languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->languages = $languages;
        if(!isset($data['teaser_ids'])){
            $data['teaser_ids'] = array();
        }
        $this->view->data = $data;
        //teasers with the same box
        $this->view->availableTeasers = Teaser_Model_TeaserMapper::getInstance()->fetchAll(array('box_code' => $data['box_code']));
        $this->view->defaultTeaserId = $defaultTeaserId;
    }
    
    public function itemDeleteAction(){
        $id = $this->_getParam('id');
        $item = new Teaser_Model_Item();
        if(!Teaser_Model_ItemMapper::getInstance()->find($id, $item)){
            $result = array(
                "success" => false,
                "message" => $this->translate("Item not found.")
            );
        }else{
            $success = Teaser_Model_ItemMapper::getInstance()->delete($item);
            $result = array(
                        "success" => $success,
                        "message" => ($success)?$this->translate("Item deleted."):$this->translate("Error deleting Item.")
                    );
        }
        return $this->_helper->json($result);
    }
    
    /**
     * Reorder items in a teaser
     * 
     * @return type 
     */
    public function reorderAction(){
        $teaserId = $this->_getParam('teaser_id');
        $items = explode(',', $this->_getParam('items'));
        
        if(!$teaserId || !is_array($items) || !count($items)){
            return $this->_helper->json(array(
                "success" => false,
                "message" => $this->translate("Parameters not provided")
            ));            
        }
        
        $teaser = new Teaser_Model_Teaser();
        if(!Teaser_Model_TeaserMapper::getInstance()->find($teaserId, $teaser)){
            return $this->_helper->json(array(
                "success" => false,
                "message" => $this->translate("Teaser not found.")
            ));
        }
        $orderNum = 0;
        foreach ($items as $itemId) {
            $orderNum++;
            $teaserId = $teaser->get_id();
            Teaser_Model_TeaserMapper::getInstance()->setItemOrder($teaserId, $itemId, $orderNum);
        }
        return $this->_helper->json(array(
            'success'   => true
        ));
    }
    
    protected function _findImageCodes($width, $height, $boxImages){
        $result = array();
        foreach ($boxImages as $imageCode => $imageData) {
            $imageOptions = $imageData['options'];
            if(     $width >= $imageOptions['minwidth'] && $width <= $imageOptions['maxwidth'] &&
                    $height >= $imageOptions['minheight'] && $height <= $imageOptions['maxheight']){
                $result[] = $imageCode;
            }
        }
        return $result;
    }
    
    /**
     * Return images from folder
     * 
     * @param string $dir
     * @param array $box
     * @param Admin_Model_FileServer $fileServer
     * @return array|false
     */
    protected function _browseFolder($dir, $box, Admin_Model_FileServer $fileServer){
        $result = array();
        //list images
        $files = array();
        $realDir = $fileServer->getRealPath($dir);
        $fileHandle = opendir($realDir);
        if ($fileHandle === FALSE) {
            return false;
        }
        clearstatcache();
        while (false !== ($file = readdir($fileHandle))) {
            if($file == '.' || $file == '..' || $file == '.DS_Store') {
                continue;
            }
            $path = $realDir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $files[] = $path;
            }
        }
        closedir($fileHandle);
        //find defined box image keys
        foreach ($files as $imagePath) {
            $imageInfo = getimagesize($imagePath);
            if(is_array($imageInfo) && count($imageInfo) >= 2){
                $result[$dir . '/' . basename($imagePath)] = $this->_findImageCodes($imageInfo[0], $imageInfo[1], $box['params']['images']);
            }
        }
        
        return $result;
    }
    
    public function unzipAction(){
        $boxCode = $this->_getParam('box_code');
        $zipFile = $this->_getParam('zip_file');
        //get teaser box
        $box = Teaser_Model_TeaserMapper::getInstance()->getBox($boxCode);
        if(!isset($zipFile) || !isset($box) || !isset($box['params']['images'])){
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => 'Invalid parameters provided'
            ));            
        }
        //init file server
        Zend_Registry::set("fileserver_helper", $this->_fileHelper);
        $fileServer = new Admin_Model_FileServer();
        //unzip package
        try {
            $fileServer->unzip($zipFile, true);
        } catch (Exception $exc) {
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => $exc->getMessage()
            ));                    
        }
        
        $dir = pathinfo($zipFile, PATHINFO_DIRNAME) . '/' . pathinfo($zipFile, PATHINFO_FILENAME);
        $result = $this->_browseFolder($dir, $box, $fileServer);
        if ($result === FALSE) {
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => 'Directory not created'
            ));
        }        
                
        return $this->_helper->json(array(
            'success'   => true,
            'message'   => 'Unzipped',
            'images'    => $result
        ));
        
    }
    
    public function dirSelectAction(){
        $boxCode = $this->_getParam('box_code');
        $dir = $this->_getParam('dir');
        //get teaser box
        $box = Teaser_Model_TeaserMapper::getInstance()->getBox($boxCode);
        if(!isset($dir) || !isset($box) || !isset($box['params']['images'])){
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => 'Invalid parameters provided'
            ));            
        }
        //init file server
        Zend_Registry::set("fileserver_helper", $this->_fileHelper);
        $fileServer = new Admin_Model_FileServer();
        
        $result = $this->_browseFolder($dir, $box, $fileServer);        
        if ($result === FALSE) {
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => 'Directory not created'
            ));
        }        
        return $this->_helper->json(array(
            'success'   => true,
            'message'   => 'Selected',
            'images'    => $result
        ));
        
    }    
}