<?php
/**
 * Cms action controller
 *
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */


class HCMS_Controller_Action_Cms extends Zend_Controller_Action
{
    /**
     *
     * @var int
     */
    protected $_applicationId = null;

    /**
     *
     * @var Application_Model_Application
     */
    protected $_application = null;

    /**
     * @var Zend_Log
     */
    protected $_logger = null;

    /**
     *
     * @var HCMS_File_Helper
     */
    protected $_fileHelper = null;

    /**
     *
     * @var boolean
     */
    protected $_isMetaFromActiveMenu = true;

    protected $_isFrontEnd = true;

    protected $_theme = null;
    
    protected $_module = null;
    
    protected $_publicDirectory = '';


    public function init(){
        $this->_initApplication();
        $this->_initTheme();
        $this->_initLog();     
        $this->_initLanguage();
        $this->_initTranslator();
        $this->_initMenus();
        $this->_initLayout();
        $this->_initFileHelper();
    }

    protected function _initApplication(){
        $bootstrap = $this->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        if(isset ($config['default']['applicationId'])){
            $this->_applicationId = $config['default']['applicationId'];
        }
        else{
            $this->_applicationId = 1;
        }        
        $this->_application = new Application_Model_Application();
        if(!Application_Model_ApplicationMapper::getInstance()->find($this->_applicationId, $this->_application)){
            throw new Zend_Exception("Application not found [$this->_applicationId]");
        }
        
        $this->_publicDirectory = (isset($config['default']['publicDirectory']))? $config['default']['publicDirectory'] : APPLICATION_PATH .'/../public';
        
        $fileConfig = HCMS_Utils::loadThemeConfig('application.php');
        if($fileConfig && isset($fileConfig['settings'])){
            $this->_application->set_settings($fileConfig['settings']);
        }
        $this->view->application = $this->_application;
        $this->view->headTitle($this->_application->get_name())->setSeparator(' - ');
        
        if($this->getRequest()->getModuleName() != ''){
            $this->_module = $this->getRequest()->getModuleName();
        }
        $this->view->module = $this->_module;
        
        
        
        //fb og properies
        $this->view->doctype('XHTML1_RDFA');
        
        $this->view->headMeta()->setProperty('og:type', 'website');
        $this->view->headMeta()->setProperty('og:url', $this->view->fullUrl());
        
        
        if($this->_application->get_name() != '') $this->view->headMeta()->setProperty('og:title', $this->_application->get_name());
        if($this->_application->get_og_settings('image') != '') $this->view->headMeta()->setProperty('og:image',  $this->view->fullUrl('/content/1/'.$this->_application->get_og_settings('image')));
        if($this->_application->get_og_settings('description') != '') $this->view->headMeta()->setProperty('og:description', $this->_application->get_og_settings('description'));
    }

    /**
     * Init theme view script paths
     * 
     * @return mixed
     */
    public function _initTheme(){        
        if(null != $this->_application->get_settings('theme')){
            $this->_theme = $this->_application->get_settings('theme');
        }
        else{
            //no theme defined
            return;
        }
        
        /**
         * create symlink if it's not exist
         */
        $createSml = $this->getRequest()->getParam('csl', 'no');
        if($createSml == 'yes'){
            $target = APPLICATION_PATH . '/../themes/' . $this->_theme . '/public';
            $symlink = $this->_publicDirectory . '/themes/' . $this->_theme;

            if(!is_link($symlink) && !is_dir($symlink)){
               symlink($target, $symlink);
            }

            foreach(glob(APPLICATION_PATH.'/modules/*', GLOB_ONLYDIR) as $module_path) {
                $module_name = str_replace(APPLICATION_PATH.'/modules/', '', $module_path);

                $target = $module_path . '/public';
                $symlink = $this->_publicDirectory . '/modules/' . $module_name;

                if(!is_link($symlink) && !is_dir($symlink)){
                   symlink($target, $symlink);
                }
            }  
        }
        
        $this->view->theme = $this->_theme;
        $themePath = APPLICATION_PATH . '/../themes/' . $this->_theme . '/views/';
        //add theme view path for cms module
        $this->view->addScriptPath($themePath . 'cms');
        //add theme view path for current module
        $this->view->addScriptPath($themePath . $this->getRequest()->getModuleName());
        //add original layout path to view scripts path
        $this->view->addScriptPath($this->_helper->layout->getLayoutPath());
        //add theme layout path
        $this->_helper->layout->setLayoutPath(APPLICATION_PATH . '/../themes/' . $this->_theme . '/layouts');
        //add theme view helpers path
        $this->view->addHelperPath(APPLICATION_PATH . '/../themes/' . $this->_theme . '/helpers', 'Theme_View_Helper');
    }

    /**
     * Get first available lang
     * 
     * @return string
     */
    protected function _getPrimaryLang(){
        $lang = Application_Model_TranslateMapper::getInstance()->getDefaultLang();
        if(!isset ($lang)){
            $lang = 'en';
        }
        return $lang;
    }

    /**
     * Check if site is single lang - if yes return lang
     * 
     * @return string|boolean
     */
    public function isSingleLang(){
        $singleLang = Zend_Controller_Front::getInstance()->getParam('singleLang');
        if(isset ($singleLang) && $singleLang != ""){
            return $singleLang;
        }
        else{
            return false;
        }
    }

    protected function _initLanguage(){
        $singleLang = Zend_Controller_Front::getInstance()->getParam('singleLang');
        if(isset ($singleLang) && $singleLang != ""){
            //1. get language from app.ini
            $language = $singleLang;
            $this->getRequest()->setParam("lang",$singleLang);            
        }
        else{
            //1. get language from request
            $language = $this->getRequest()->getParam("lang");
        }
        //$this->_log("Request: " . json_encode($this->getRequest()->getParams()), Zend_Log::DEBUG);
        if($language == ''){
            $language = null;
        }
        //2. get language from cookie
        if(!isset ($language)){
            $language = $this->getRequest()->getCookie("saved_lang",null);
        }
        //3. get from geoip
        if(!isset ($language)){
            $country = new Application_Model_Country();
            if(Application_Model_CountryMapper::getInstance()->getCountryByGeoIp(HCMS_Utils::getRealIpAddr(), $country)){
                $language = strtolower($country->get_def_lang());
            }
        }        
        //4. get default
        if(!isset ($language)){
            $language = $this->_getPrimaryLang();
        }

        //check if lang available
        if(!HCMS_Translate_Adapter_Db::isLangAvailable($language, $this->_isFrontEnd)){
            $language = $this->_getPrimaryLang();
        }
        //redirect if lang is not in url
        if($language != $this->getRequest()->getParam("lang")){
            //redirect only if lang not exists...otherwise let it 404
            if(!$this->getRequest()->getParam("lang")){
            $this->_log("redirecting to $language", Zend_Log::DEBUG);
            $redirector = new Zend_Controller_Action_Helper_Redirector();
            $redirector->gotoRouteAndExit(array('lang'=>$language),'default',false);
        }
        }

        //activate lang
        HCMS_Translate_Adapter_Db::activate($language);
        //store lang in cookie
        $cookieRes = setcookie('saved_lang', $language, time() + 3600,'/', null, false, true);
        if(!$cookieRes){
            $this->_logger->log("Error storing lang cookie", Zend_Log::WARN);
        }
        //and in router
        Zend_Controller_Front::getInstance()->getRouter()->setGlobalParam('lang', $language);
        //set view and js var
        if($this->view){
            $this->view->availableLang =  Application_Model_TranslateMapper::getInstance()->getLanguages();
            $this->view->currLang = $language;
            $this->view->headScript()->appendScript("var CURR_LANG = '" . $language . "';");
            $this->view->singleLang = $this->isSingleLang();
            //$this->addLinkAlternateLang();
        }
        //define php const
        if(!defined("CURR_LANG")){
            define("CURR_LANG", $language);
        }  
    }

    protected function _initMenus(){
                
        $bootstrap = $this->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        $menus = array('main','footer');        
        if(isset ($config['menuSettings']['menus']) && count($config['menuSettings']['menus'])){
            $menus = $config['menuSettings']['menus'];
        }
        
        $menuItemMapper = Cms_Model_MenuItemMapper::getInstance();
        $this->view->menuItems = array();
        foreach ($menus as $currMenu) {
            $this->view->menuItems[$currMenu] = new Zend_Navigation($menuItemMapper->fetchZendNavigationArray(array(
                'application_id'    => $this->_application->get_id(),
                'menu'              => $currMenu,
                'lang'              => CURR_LANG,
                'visible_only'      => true
            ),array(), false));
        }
        //set meta from menu
        if($this->_isMetaFromActiveMenu){
            $this->_populateMetaFromActiveMenu();
        }
    }

    public function _initTranslator(){
        if(Zend_Registry::isRegistered('Zend_Translate')){
            $this->_translator = Zend_Registry::get('Zend_Translate');
        }
    }

    public function _initLog(){
        if (Zend_Registry::isRegistered('Zend_Log')) {
            $this->_logger = Zend_Registry::get('Zend_Log');
        }
    }    

    protected function _initLayout(){
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

    /**
     * Translate a string
     *
     * @param string $msg
     * @return string
     */
    public function translate($msg){
        return Zend_Registry::get('Zend_Translate')->_($msg);
    }

    /**
     * If page is not part of navigation you can still activate parent menu item
     * by url path on the upper level
     *
     * @param string $menuName
     */
    public function activateParentMenuByUrl($menuName = 'main'){
        $uri = dirname(rtrim($this->_request->getPathInfo(), '/'));
        if (($activeNav = $this->view->navigation($this->view->menuItems[$menuName])->findByHref($uri)) !== null) {
            $activeNav->active = true;
        }
    }

    public function setActiveMenu($menuId){
        $menu = $this->view->navigation()->findOneBy('id', $menuId);
        if(isset ($menu)){
            $menu->setActive();
        }
    }

    /**
     * Get active page from zend navigation
     * 
     * @return Zend_Navigation_Page_Mvc|null
     */
    public function getActiveMenuPage(){
        $activePage = null;
        foreach ($this->view->menuItems as $currMenu => $menuContainer) {
            $activeMenu = $this->view->navigation()->findActive($menuContainer);
            if(isset ($activeMenu) && isset ($activeMenu['page'])){
                /*@var $activePage Zend_Navigation_Page_Mvc */
                $activePage =  $activeMenu['page'];
                break;
            }
        }
        return $activePage;
    }

    /**
     * Get active menu item entity from zend navigation
     * 
     * @return Cms_Model_MenuItem|null
     */
    public function getActiveMenuItem(){
        $activePage = $this->getActiveMenuPage();
        if(!isset ($activePage)){
            return null;
        }
        return $activePage->entity;
    }

    /**
     * Populate meta from active menu item
     *
     * @return mixed
     */
    protected function _populateMetaFromActiveMenu(){
        $menuItem = $this->getActiveMenuItem();
        if(!isset ($menuItem)){
            return false;
        }        
        $meta = $menuItem->get_meta();
        if(!is_array($meta) || !count($meta)){
            $this->view->headTitle($menuItem->get_name(),Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
            return;
        }
        //set custom title
        if(isset($meta['title'])){
            $this->view->headTitle($meta['title'],Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
            unset($meta['title']);
        } else {
            $this->view->headTitle($menuItem->get_name(),Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
        }
        foreach ($meta as $key => $value) {
            if($value != null && $value != ''){
                $this->view->headMeta()->appendName($key, $value);
            }
        }
    }

    protected function _initFileHelper(){
        $this->_fileHelper = new HCMS_File_Helper($this->_application, $this->getInvokeArg('bootstrap')->getOption('fileserver'));
        Zend_Registry::set('fileHelper', $this->_fileHelper);
        $this->_filePaths = $this->_fileHelper->getPath("");
        $this->view->fileWebRoot = $this->_filePaths['web'];
    }
    
    /**
     * Define in head/links all alternate version of this page per available langs
     * 
     * @return
     */
    protected function addLinkAlternateLang()
    {
        if(!isset($this->view) || !isset($this->view->availableLang)){
            return;
        }
        foreach ($this->view->availableLang as $code => $value) {
            if(!isset ($value['front_enabled']) || !$value['front_enabled']){
                continue;
            }
            try {
                $this->view->headLink()->appendAlternate($this->view->switchLang($code), 'text/html', null, array('hreflang' => $code));
            } catch (Exception $exc) {
            }            
        }
    }
}