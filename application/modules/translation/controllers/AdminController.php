<?php

/**
 * Translation Admin controller
 *
 * @package Translation
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Translation_AdminController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;

    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
    }

    /**
     * index pages
     */
    public function indexAction() {
        $error = '';
        $option = Translation_Model_TranslationMapper::getInstance()->getOptions($this->view);
        HCMS_Utils::generateFormatedOptions(Translation_Model_TranslationMapper::getInstance()->getTranslationSection($error), $section, $sectionValue, false);
        $this->view->section = $sectionValue;
        
        $this->view->option = json_encode($option);
    }

   /**
     *  List ajax action
     */
    public function listAction() {
        $parameters = array();
        $parameters =  $this->_request->getParams();
//        $parameters['sidx']= $this->_request->getParam('sidx');
//        $parameters['sord']= $this->_request->getParam('sord');
        //paging
        if(isset($parameters['order']) && $parameters['order'] != ''){
            $order = explode(" ", $parameters['order']);
            $parameters['sord'] = $order[1];
        }
        $page = $this->_request->getParam('page');
        if(isset($parameters['perPage'])){
            $perPage = $parameters['perPage']; 
            $parameters['rows'] = $parameters['perPage'];
        }            
        else
            $perPage = $this->_request->getParam('rows');
        
        if(!isset ($page) || $page < 1){
            $page = 1;
        }
        if(!isset ($perPage) || $perPage < 1 || $perPage > 300){
            $perPage = 10;
        }

        $respons = Translation_Model_TranslationMapper::getInstance()->getTranslations($page, $perPage, $parameters);
        $this->_helper->json->sendJson($respons);
        
    }
    
    /**
     * Delete ajax action
     */
    public function deleteAction(){
        $key = $this->_getParam('id');
        Translation_Model_TranslationMapper::getInstance()->delete($key);
        $this->_helper->json->sendJson(array(
                                'success'   => true,
                                'message'   => $this->view->translate('Data Deleted.')
        ));
    }
    
    /**
     *  Edit ajax action
     */
    public function editAction(){
        $data = $this->getRequest()->getParam('data');
        $id = $this->_getParam('id');
        $editable = $this->_getParam('editable');
        
        if($this->getRequest()->getMethod() == 'POST'){
            $data = $this->getRequest()->getPost('data');

            /*
             * Check if key and data are same. If they are not, then insert new translate. Else, update existing translation key
             */
            if($data['key'] != $data['id'] && $data['id'] == ''){
                
                $form = new Translation_Form_Edit($data);
                if(!$form->isValid())
                    $this->_formHelper->returnError($form->getMessages());

                $form = new Translation_Form_Edit($data);
                if(!$form->isValid())
                    $this->_formHelper->returnError($form->getMessages());
                foreach($data['lang'] as $lang_id => $value){
                    $dataSaved = Translation_Model_TranslationMapper::getInstance()->save($data['key'], $lang_id, htmlspecialchars_decode($value), $data['section'], false);
                }
                $this->_helper->json->sendJson(array(
                        'success' => true,
                        'message' => $this->view->translate('Data Saved.')
                ));
                
            }else{
                
                if($data['id'] == ''){
                    $form = new Translation_Form_Edit($data);
                    if(!$form->isValid())
                        $this->_formHelper->returnError($form->getMessages());
                }
                foreach($data['lang'] as $lang_id => $value){
                    $dataSaved = Translation_Model_TranslationMapper::getInstance()->save($data['key'], $lang_id, htmlspecialchars_decode($value), $data['section'], false);   
                }
                $this->_helper->json->sendJson(array(
                        'success'   => true,
                        'message'   => $this->view->translate('Data Updated.')
                )); 
            }
        }
        else{

          if($id != 'null'){                
                if(strpos($id, "<") !== false || strpos($id, ">") !== false || strpos($id, "/>") !== false){                   
                    $data['_search'] = $id;
                }else{                    
                    $data['_search'] = htmlspecialchars($id);                    
                    $data['_search'] = str_replace("&amp;", "&", $data['_search']);
                }
                $data['editable'] = $editable;

                $data['sidx'] = 't.key';
                $data['sord'] = 'asc';

                $response = Translation_Model_TranslationMapper::getInstance()->getTranslations(1, 1, $data);
                return $this->view->data = $response->rows[0];
            }  
        }
    }
    
    
    public function exportToExcelAction(){
        
        $data = $this->getRequest()->getPost('data');
        $data['grid_params']=json_decode($data["grid_params"], true); 
        
        $submit = $this->getRequest()->getPost('submit');
        if ($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action cancelled.'));
        }
        if ($this->getRequest()->isPost()){
            if(isset($data["language_id"]) && count($data["language_id"]) > 0){
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'save-excel', 'data'=> json_encode($data))), $this->translate('Translation successfully exported. Save the file.'), $data); //
            }
            else{
                $this->_formHelper->returnError( array("language_id"=>array("isEmpty"=> $this->translate("Select the column you want to export."))));
            }
        }else{
            $data = $this->getRequest()->getParams();
            $translateLanguages = Translation_Model_TranslationMapper::getInstance()->getTranslateLanguage();
            $this->view->translateLanguages = $translateLanguages;
            $grid_params = array('_search' => $data['_search'], 'rows' => $data['rows'], 'page' => $data['page'], 'sidx' => $data['sidx'], 'sord' => $data['sord']); 
            $this->view->grid_params = json_encode($grid_params);
        }
    }
    
    public function saveExcelAction(){
        $data = $this->getRequest()->getParam("data");
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        Translation_Model_TranslationMapper::getInstance()->exportTranslation($data["language_id"], $data["grid_params"]);
        die();
    }
    
    public function importFromExcelAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($this->getRequest()->isPost()){
            
            
            try{
                $adapter = new Zend_File_Transfer_Adapter_Http();
                $adapter->addValidator("Count",false, array("min"=>1, "max"=>1))
                        ->addValidator("Size",false,array("max"=>1000000))
                        ->addValidator("Extension",false,array("extension"=>"xls", "case" => true));

                $adapter->setDestination(APPLICATION_PATH . "/../tmp/");

                $files = $adapter->getFileInfo();
                foreach($files as $fieldname=>$fileinfo){
                    if (($adapter->isUploaded($fileinfo["name"]))&& ($adapter->isValid($fileinfo['name']))){
                        $extension = substr($fileinfo['name'], strrpos($fileinfo['name'], '.') + 1);
                        $filename = 'file_'.date('Ymdhs').'.'.$extension;
                        $adapter->addFilter('Rename',array('target'=>APPLICATION_PATH . "/../tmp/".$filename,'overwrite'=>true));
                        $adapter->receive($fileinfo["name"]);
                    }
                }
                if(count($adapter->getMessages()) > 0 ){
                    return $this->returnAjaxResult(FALSE, $adapter->getMessages());
                }else{
                    $errors = array();
                    $files = $adapter->getFileInfo();
                    foreach ($files as $file){
                        Translation_Model_TranslationMapper::getInstance()->importTranslation($file['destination']."/".$file['name'], $errors);
                    }
                    
                    if(count($errors)>0){
                        foreach ($errors as $error){
                            $message[]= $error["message"];
                        }
                        return $this->returnAjaxResult(FALSE, $message);
                    }else{                                                
                        $url = $this->_helper->url->url(array('controller' => 'admin', 'action' => 'index', 'module' => 'translation'));                        
                        $this->_redirect($url);
                    }
                };
            }
            catch (Exception $ex){
               return $this->returnAjaxResult(FALSE, $ex->getMessage());
            }
        }
        
    }
    
    private function returnAjaxResult($success,$message){
         echo json_encode(array(
            'success'   => $success,
            'message'   => $message
        ));
    }
}
