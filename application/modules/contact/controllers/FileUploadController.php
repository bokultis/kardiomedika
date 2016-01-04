<?php

/**
 * Description of FileUploadConrtoller
 * 
 * @package Modules
 * @subpackage Contact
 * @copyright Horisen
 * @author boris
 */
class Contact_FileUploadController extends HCMS_Controller_Action_Cms {
    
    protected $_formId;
    protected $_formParams = array();
    protected $_fields = array();
    protected $_globalSettings = array();
    
    /**
     * HCMS_File_GenericHelper
     */
    protected $_genericFileHelper = null;
    
    protected function _initLayout() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        parent::_initLayout();
    }
    
    protected function _initFileHelper(){
        $this->_genericFileHelper = new HCMS_File_GenericHelper($this->_application, $this->getInvokeArg('bootstrap')->getOption('fileserver'));
        Zend_Registry::set('genericFileHelper', $this->_genericFileHelper);
        $this->_filePaths = $this->_genericFileHelper->getPath("");
        $this->view->fileWebRoot = $this->_filePaths['web'];
    }
    
    protected function _loadParams(){      
        $this->_globalSettings = HCMS_Utils::loadThemeConfig('config.php', 'contact');
        $fieldTypes = HCMS_Utils::loadThemeConfig('types.php', 'contact');
        if($this->_request->getParam('form_id')){
            $this->_formId = $this->_request->getParam('form_id');
        }
        if(!isset($this->_globalSettings['forms'][$this->_formId])){            
            throw new Exception("Form not found");
        }
        $this->_formParams = $this->_globalSettings['forms'][$this->_formId];
        $this->_fields = Contact_Form_Generic::getFieldsArr($fieldTypes, $this->_formParams['fields']);
    }
    
    
    /**
     *Upload File
     *
     */
    public function uploadAction() {
        
        $this->_loadParams();   
                
        $dir = $this->_fields[$this->_request->getParam('field_id')]['params']['dir'];

        
        if(!Zend_Session::sessionExists() || !Zend_Session::isStarted()){
            Zend_Session::start();
        }
        $uniqueName = Zend_Session::getId();
        $this->_genericFileHelper->createFieldDir($dir . DIRECTORY_SEPARATOR . stripcslashes($uniqueName), true);
        
        $destination = $dir . DIRECTORY_SEPARATOR . stripcslashes($uniqueName);
        
        $uploadSettings = $this->getParams($this->_request->getParam('field_id'));

        if(!isset ($uploadSettings)){
           //do something bcs there is no file types
        }
        $uploadSettings = array_merge($uploadSettings, array(
            'dir'   => $destination,
            'field' => $this->_request->getParam('field_id')
        ));
        
        $result = $this->_genericFileHelper->upload($uploadSettings);
        if($result === false) {
            $result = array(
                'success'=> false,
                'files' => array()
            );
            $lastError = $this->_genericFileHelper->getLastErrorMessage();
            if($lastError != ''){
                $result['error'] = $this->translate($lastError);
            }
            echo json_encode($result);

        }
        else{
            $result = array(
                'success'=> true,
                'files' => array(
                    $result
                ),
                
                'path'   => $result['path']
            );
            $lastError = $this->_genericFileHelper->getLastErrorMessage();
            if($lastError != ''){
                $result['error'] = $this->translate($lastError);
            }
            echo json_encode($result);
        }
        die;
    }

    /**
     * Getting params for a field in a clean and safe way
     */
    protected function getParams($field_id, $key=null){
        if(!isset ($key)){
            return $this->_fields[$field_id]['params'];
        }
        else{
            if(isset ($this->_fields[$field_id]['params'][$key])){
                return $this->_fields[$field_id]['params'][$key];
            }
            else{
                return null;
            }
        }
    }
    
     /**
     * Function for changing array key names
     *
     * @param array $existing
     * @param array $newKeys
     * @return array 
     */
    function newKeys($existing, $newKeys) {
        // a really simple check that the arrays are the same size
        if (count($existing) !== count($newKeys))
            return false; // or pipe out a useful message, or chuck exception

        $data = array();  // set up a return array
        $i = 0;
        foreach ($existing as $k => $v) {
            $data[$newKeys[$i]] = $v;  // build up the new array
            $i++;
        }
        return $data; // return it
    }

//    public function indexAction() {
//        $upload_handler = new HCMS_File_UploadHandler();
//    }
    
}
