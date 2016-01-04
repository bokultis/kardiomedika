<?php
/**
 * View helper to enable wysiwyg editor in front
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_FrontAdmin extends Zend_View_Helper_Abstract {
    
    protected static $enabled = null;
    protected  $module = '';
    protected $initialized = false;
    
    public function isEnabled(){
        if(isset(self::$enabled)){
            return self::$enabled;
        }
        //check session
        $session = new Zend_Session_Namespace('admin_front');
        self::$enabled = $session->enabled;
        return self::$enabled;
        //more check TODO        
    }
    
    /**
     * Render top header
     * 
     * @return string 
     */
    public function init($module = ''){
        if($this->initialized){
            return false;
        }
        $this->initialized = true;
        $this->module = $module;
        $this->isEnabled();
        if(!self::$enabled){
            return false;
        }
        $this->view->headScript()->appendFile('/plugins/tinymce/4.1/tinymce.min.js');
        $this->view->headScript()->appendFile('/js/ajaxLoader.js');
        $this->view->headScript()->appendFile('/js/php.js');

        $this->view->headScript()->appendFile('/plugins/flashmessenger/jquery.flashmessenger.js');
        $this->view->headScript()->appendFile('/js/init.js');
        $this->view->headScript()->appendFile('/js/json2.js');
        
        $this->view->headScript()->appendFile('/bootstrap/js/bootstrap.js');
        $this->view->headScript()->appendFile('/plugins/bootstrap-modal/js/bootstrap-modal.js');
        $this->view->headScript()->appendFile('/plugins/bootstrap-modal/js/bootstrap-modalmanager.js'); 
        $this->view->headScript()->appendFile('/plugins/imagebrowser/bs/jquery.imagebrowserdialog.js');
        $this->view->headScript()->appendFile('/plugins/serverbrowser/bs/jquery.filebrowserdialog.js');
        $this->view->headScript()->appendFile('/' . CURR_LANG . '/default/lang-js');
        
        $file = "themes/". $this->view->theme ."/js/tiny-front-extension.js";
        if(file_exists($file)){
            $this->view->headScript()->appendFile("/".$file);
        }
        
        $this->view->headScript()->appendFile('/modules/cms/js/front-admin.js');   
	
        $this->view->headLink()->prependStylesheet('/plugins/flashmessenger/flashmessenger.css');
        $this->view->headLink()->prependStylesheet('/modules/cms/css/front-admin.css');
        $this->view->headLink()->prependStylesheet('/modules/admin/css/file-menager.css');
        $this->view->headLink()->prependStylesheet('/modules/admin/css/horisen-font.css');
	$this->view->headLink()->prependStylesheet('/modules/admin/css/bootstrap-btns.css');
        
        $this->view->headLink()->prependStylesheet( '/plugins/bootstrap-modal/css/bootstrap-modal.css');
	$this->view->headLink()->prependStylesheet( '/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css');
	$this->view->headLink()->prependStylesheet('/modules/admin/css/bootstrap-modal.css');
	//$this->view->headLink()->prependStylesheet('/modules/admin/css/bootstrap-grid.css');
         
    }    
    
    /**
     * Render top header
     * 
     * @return string 
     */
    public function renderHeader(){
        if(!self::$enabled || $this->module != 'cms'){
            return '';
        }
        return $this->view->render('front-admin.phtml');
    }
    
    /**
     * Enable inline wysiwyg editor in front
     * 
     * @param string $content
     * @param Cms_Model_Page $page 
     */
    public function frontAdmin() {
        $this->init($this->view->module);
        return $this;
    }    

    /**
     * Enable inline wysiwyg editor in front
     * 
     * @param string $content
     * @param Cms_Model_Page $page 
     */
    public function renderEditable($content, Cms_Model_Page $page) {
        if(!self::$enabled){
            return $content;
        }        
        //only for html format
        if($page->get_format() != 'html'){
            return $content;
        }
        //more check TODO        
        return '<div class="editable" data-id="' . $page->get_id(). '">' . $content . '</div>';
    }
    
    /**
     * Enable inline wysiwyg editor in front if page is set in view
     * 
     * @param string $content
     * @param Cms_Model_Page $page 
     */
    public function renderPage($content) {
        if(!isset($this->view->page)){
            return $content;
        }
        return $this->renderEditable($content, $this->view->page);
    }    
}
