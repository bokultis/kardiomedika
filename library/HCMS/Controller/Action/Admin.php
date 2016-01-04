<?php
/**
 * Cms admin action controller - base for all admin controllers
 *
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */

class HCMS_Controller_Action_Admin extends HCMS_Controller_Action_Cms {

    protected $_checkAuth = true;

    protected $_authResourse = 'admin';
    protected $_authPrivilege = 'access';
    
    /**
     *
     * @var Auth_Model_User
     */
    protected $_admin;
    
    protected $_versionInfo;

    /**
     *
     * @var Application_Model_Module
     */
    protected $_module = null;

    protected $_isFrontEnd = false;

    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_sessionAdmin = null;

    public function init() {
        //set timeout
        $this->_sessionAdmin = new Zend_Session_Namespace(Zend_Auth_Storage_Session::NAMESPACE_DEFAULT);
        $this->_sessionAdmin->setExpirationSeconds(30 * 60);        
        //load acl
        $aclLoader = HCMS_Acl_Loader::getInstance();
        $aclLoader->load();
        
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_admin = null;
        }
        else{
            $this->_admin = Zend_Auth::getInstance()->getIdentity();
            $aclLoader->setCurrentRoleCode($aclLoader->getRoleCode($this->_admin->get_role_id()));
        }
        $this->view->admin = $this->_admin;
        if($this->_checkAuth){
            $this->_checkAuthorization();
        }
        
        $this->_redirect_to_ssl();
         
        $this->_checkIP();
        
        //set ACL object for Zend_Navigation
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($aclLoader->getAcl());
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($aclLoader->getCurrentRoleCode());

        $this->_initVersionInfo();
        $this->_module = new Application_Model_Module();
        if(Application_Model_ModuleMapper::getInstance()->findByCode($this->getRequest()->getModuleName(), $this->_module)){
            $this->view->moduleSettings = $this->_module->get_settings();
        }
        parent::init();        
    }

    /**
     * Check authorization
     */
    protected function _checkAuthorization() {
        $routeName = Zend_Controller_Front::getInstance()->getRouter()->hasRoute('admin')? 'admin' : 'default';
        
        $lang = $this->_request->getParam('lang');
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            //if ajax request
            if($this->getRequest()->isXmlHttpRequest()) {
                /*return $this->getHelper('json')->direct(array(
                    'success'   => false,
                    'message'   => $this->view->translate("Please login first")
                ));*/
                throw new Zend_Controller_Action_Exception("Please login first",403);
            }
            //store to return
            $this->returnHere();            
            //redirect to login page
            $this->_redirect($this->view->url(array('controller' => 'index', 'action' => 'login', 'module' => 'admin', 'lang' => $lang), $routeName, true));
        }
        $aclLoader = HCMS_Acl_Loader::getInstance();
        //check permission
        if(!$aclLoader->getAcl()->isAllowed($aclLoader->getCurrentRoleCode(), $this->_authResourse, $this->_authPrivilege)){
            //redirect to login page
            $this->_redirect($this->view->url(array('module' => 'admin', 'controller' => 'index', 'action' => 'login', 'lang' => $lang), $routeName, true));
            throw new Zend_Controller_Action_Exception("You are not allowed to access this page",403);
        }
    }

    /**
     * Get BS option
     * @param string $name
     * @param string $section
     * @return null|mixed
     */
    protected function _getBootstrapOption($name, $section = 'default', $defaultValue = null){
        $bootstrap = $this->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        if(isset($config[$section][$name])){
            return $config[$section][$name];
        }
        return $defaultValue;
    }

    /**
     * Check permission per IP address
     */
    protected function _checkIP() {                
        //fetch ip restriction from application.ini
        $ipRestriction = $this->_getBootstrapOption('ip_restriction');
        if(!$ipRestriction || $ipRestriction == ''){
            return false;            
        }
        $ipRestrictions = explode(",",$ipRestriction);
        //Check permission per IP address
        if(!in_array(HCMS_Utils::getRealIpAddr(), $ipRestrictions)){                                
            throw new Zend_Controller_Action_Exception("You are not allowed to access this page",403);
        }
    }
    
    /**
     * Force SSL 
     */
    protected function _redirect_to_ssl() {
        if ($this->_getBootstrapOption('admin_force_ssl') && $_SERVER['SERVER_PORT'] != 443) {
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            die();
        }
    }

    protected function _initVersionInfo(){
        if(file_exists(APPLICATION_PATH . "/version.php")){
            $this->_versionInfo = require APPLICATION_PATH . "/version.php";
        }
        else{
            $this->_versionInfo = array();
        }
        $this->view->versionInfo = $this->_versionInfo;
    }

    protected function _initLayout(){
        $layout = Zend_Layout::getMvcInstance();
        $layout ->setLayoutPath(APPLICATION_PATH . '/modules/admin/layouts')
                ->setLayout('admin');
        $this->view->headScript()->prependFile('/' . CURR_LANG . '/default/lang-js');
    }

    protected function _initMenus(){
        $navigation = HCMS_Utils::loadThemeConfig('navigation.php', 'admin');
        $this->view->navigation(
                new Zend_Navigation($navigation)
        );
    }
    
    /**
     * save url to return here in future
     */
    public function returnHere()
    {
        $this->setReturnUrl($_SERVER['REQUEST_URI']);
}
    
    /**
     * redirect to saved return url
     *
     * @param string $defaultUrl
     * @param string $message
     * @param string $messageType
     * @param bool $prependBase
     * @param bool $prepandLang
     */
    public function returnThere($defaultUrl = '')
    {
        $url = $this->getReturnUrl($defaultUrl);
        $this->_redirect($url);
    }    
    
    
    /**
     * Set url to be stored in session to be redirected back there
     *
     * Url is actually request URI and should be including base path!
     *
     * @param string $url
     */
    public function setReturnUrl($url) {
        $this->_sessionAdmin->returnUrl = $url;
    }

    /**
     * Reset session stored url.
     */
    public function clearReturnUrl() {
        $this->setReturnUrl(null);
    }

    /**
     * Get url stored in session to be redirected back there
     *
     * @param string $defaultUrl
     * @param boolean $reset     
     * @return string|null
     */
    public function getReturnUrl($defaultUrl = '', $reset = true) {
        if(isset ($this->_sessionAdmin->returnUrl)){
            $value = $this->_sessionAdmin->returnUrl;
            if($reset){
                $this->clearReturnUrl();
            }
            return $value;
        }
        else{
            if($defaultUrl != ''){
                return $defaultUrl;
            }
            else{
                return $this->view->url(array('module' => 'admin', 'controller' => 'index', 'action' => 'index'));
            }
            return null;
        }

    }
    
}