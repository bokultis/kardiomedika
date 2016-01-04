<?php

/**
 * Admin Index Controller
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author milan
 */
class Admin_IndexController extends HCMS_Controller_Action_Admin {

    public function init() {
        //if login action do not check
        if ($this->getRequest()->getActionName() == 'login') {
            $this->_checkAuth = false;
        }
        parent::init();
    }

    protected function _updateAttemp(Auth_Model_User $user, $count){
        $date = new Zend_Date();
        $user->set_attempt_login_dt($date->toString('yyyy-MM-dd HH:mm:ss'));
        $user->set_attempt_login($count);
        //update user but not set new password
        $user->set_password("");
        Auth_Model_UserMapper::getInstance()->getInstance()->save($user);
    }

    protected function _setLoginError($errorMessage = 'Wrong username or password'){
        $this->view->auth = $this->_getParam('auth');
        $this->view->messages = $this->translate($errorMessage);
    }

       
    /**
     * Login action
     */
    public function loginAction() {
        $this->view->layout()->setLayout('login');

        $total_number_attempt = $this->_getBootstrapOption('total_number_attempt', 'default', 3);
        $lock_login_time = $this->_getBootstrapOption('lock_login_time', 'default', 180);
        $expire_password = 3600 * 24 * $this->_getBootstrapOption('expire_password_day', 'default', 90);

        $user = new Auth_Model_User();
        //login
        if ($this->getRequest()->isPost()) {            
            //username found
            //$isExistUser = Auth_Model_UserMapper::getInstance()->getInstance()->findByUsername($this->_getParam('username', ""), $user);
            $isExistUser = Auth_Model_UserMapper::getInstance()->getInstance()->findByCredentials($this->_getParam('auth', ""), $user);
           
            if($isExistUser){
                $aclLoader = HCMS_Acl_Loader::getInstance();
                //check permission
                $isMaster = $aclLoader->getAcl()->isAllowed($aclLoader->getRoleCode($user->get_role_id()), "admin", "master");                
                //password expired for non master
                //echo $user->get_changed_password_dt();die('<br>here');
                if (!$isMaster && strtotime($user->get_changed_password_dt()) + $expire_password < time()) {
                    $this->sendNotificationEmail($this->_application->get_name() . " - "  .$this->translate("your password expired. Please check with your system admin how to re-activate your account."), array(
                        "subject"   => $this->_application->get_name() . " - Your password is expired",
                        "to_emails" => array($user->get_email())
                    ), CURR_LANG);                    
                    return $this->_setLoginError();
                }                
                //unlock attempts
                if ($user->get_attempt_login() >= $total_number_attempt) {                    
                    if (strtotime($user->get_attempt_login_dt()) + $lock_login_time < time()) {
                        $this->_updateAttemp($user, 0);
                    } else {                        
                        return $this->_setLoginError();
                    }                    
                }
            }            
            $adapter = new Admin_Model_Auth_Adapter($this->_applicationId, $this->_getParam('auth'), $this->_getParam('password'));
            $result = Zend_Auth::getInstance()->authenticate($adapter);            
            if ($result->isValid()) {
                //updated logged time
                Auth_Model_UserMapper::getInstance()->getInstance()->updateUserLogged($result->getIdentity());
                $this->_updateAttemp($user, 0);

                Zend_Session::regenerateId();
                $defaultUrl = $this->view->url(array('module' => 'admin', 'controller' => 'index', 'action' => 'index'), 'default', true);
                return $this->returnThere($defaultUrl);
            } else {
                if($isExistUser){                    
                    $this->_updateAttemp($user, $user->get_attempt_login() + 1);
                    //send notification                    
                    if($user->get_attempt_login() >= $total_number_attempt){
                        
                        $this->sendNotificationEmail($this->_application->get_name() . " - "  .$this->translate("your account is temporarily blocked due to too many invalid login attempts"), array(
                            "subject"   => $this->_application->get_name() . " - Your account is temporarily blocked",
                            "to_emails" => array($user->get_email())
                        ), CURR_LANG);
                    }                    
                }
                return $this->_setLoginError(implode(' ', $result->getMessages()));
            }
        }
    }

    /**
     * Logout action
     */
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        //redirect to landing page
        $this->_redirect($this->view->url(array('controller' => 'index', 'action' => 'login')));
    }

    public function filemanagerAction() {
        
    }

    public function widgetAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
        }

        $this->view->hostingQuota = $this->_fileHelper->getQuota();
        $this->view->hostingFreeSpace = $this->_fileHelper->getFreeSpace();
        $this->view->hostingUsed = $this->view->hostingQuota - $this->view->hostingFreeSpace;
        $this->view->hostingQuotaStr = HCMS_Utils::formatBytes($this->view->hostingQuota);
        $this->view->hostingFreeSpaceStr = HCMS_Utils::formatBytes($this->view->hostingFreeSpace);
        $this->view->hostingUsedStr = HCMS_Utils::formatBytes($this->view->hostingUsed);
    }

    public function indexAction() {
        //get user object
        $user = new Auth_Model_User();
        Auth_Model_UserMapper::getInstance()->find($this->_admin->get_id(), $user);
        
        $bootstrap = $this->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        $this->view->clientId = isset($config['googleapi']['analitycs']['clientId']) && $config['googleapi']['analitycs']['clientId'] != '' ? $config['googleapi']['analitycs']['clientId']:'';
        
        
        //store dashbord
        if($this->getRequest()->isXmlHttpRequest() && $this->getRequest()->isPost()) {
            $dashboard = $this->_getParam('dashboard');
            if(!isset ($dashboard)){
                $dashboard = array();
            }
            $userData = $user->get_data();
            $userData['dashboard'] = $dashboard;
            $user->set_data($userData);
            $user->set_password(null);
            Auth_Model_UserMapper::getInstance()->save($user);
            return $this->getHelper('json')->direct(array(
                'success'   => true
            ));
        }
        $this->view->widgetClasses = array();
        $this->view->widgetJsFiles = array();
        $this->view->widgetCssFiles = array();
        //list modules
        $modules = Application_Model_ModuleMapper::getInstance()->fetchAll(array('status'=>'active'));
        /* @var $module Application_Model_Module */
        foreach ($modules as $module) {
            $widgets = $module->get_data('widgets');
            if(!isset ($widgets)){
                continue;
            }
            foreach ($widgets as $widgetClass => $widgetArr) {
                $this->view->widgetClasses[] = $widgetClass;
                foreach ($widgetArr['jsFiles'] as $jsFile) {
                    $this->view->widgetJsFiles[] = $jsFile;
                }
                foreach ($widgetArr['cssFiles'] as $cssFile) {
                    $this->view->widgetCssFiles[] = $cssFile;
                }
            }
        }
        //get configured user's dashboard
        $userDashboard = $user->get_data('dashboard');
        if(!isset ($userDashboard)){            
            $userDashboard = array(
                'region1'=> array(
                    'widgets'=> array(
                    )
                ),
                'region2'=> array(
                    'widgets'=> array(
                    )
                )
            );
            $i = 0;
            foreach ($this->view->widgetClasses as $widgetClass) {
                $widgetArr = array(
                    'componentClass' => $widgetClass,
                    'settings' => array()
                );
                $regionIndex = ($i % 2) + 1;
                $userDashboard['region' . $regionIndex]['widgets'][] = $widgetArr;
                $i++;
            }
        }
        $this->view->userDashboard = $userDashboard;
    }
    
    /**
     * Get email content
     * 
     * @param Zend_View $emailView
     * @param array $emailParams
     * @param string $templateName
     * @param string $language
     * @return string
     */
    protected function getEmailBody(Zend_View $emailView, array $emailParams, $templateName, $language) {
        $content = $emailView->render(sprintf($templateName, $language));
        if (!isset($emailParams['layout'])) {
            return $content;
        }
        $emailView->body = $content;
        return $emailView->render(sprintf($emailParams['layout'], $language));
    }
    
    protected function sendNotificationEmail($message, $emailParams, $language, $template = 'notification.phtml') {
        $emailParams = array_merge($this->_application->get_email_settings(), $emailParams);
        //print_r($emailParams); die();

        $transport = HCMS_Email_TransportFactory::createFactory($emailParams);
        //init view        
        $emailView = new Zend_View();
        $emailView->setScriptPath($this->getFrontController()->getModuleDirectory('admin') . '/views/scripts/email_templates/');
        $mvcView = clone Zend_Layout::getMvcInstance()->getView();
        if (isset($mvcView->theme)) {
            $emailView->addScriptPath(APPLICATION_PATH . '/../themes/' . $mvcView->theme . '/views/admin/email_templates/');
        }
        $emailView->assign(array(
            'application' => $this->_application,
            'message' => $message,
            'lang' => $language,
            'serverUrl' => $this->view->serverUrl(),
            'imagesUrl' => isset($mvcView->theme) ? $this->view->serverUrl() . '/themes/' . $mvcView->theme . '/images/email/' : $this->view->serverUrl() . '/images/email/'
        ));
        
        $body = $this->getEmailBody($emailView, $emailParams, $template, $language);

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($body);
        $mail->setFrom($emailParams['from_email'], $emailParams['from_name']);

        foreach ($emailParams['to_emails'] as $toEmail) {
            if(is_array($toEmail)){
                $mail->addTo($toEmail['email'], $toEmail['name']);
            } else {
                $mail->addTo($toEmail);
            }
            
        }
        $mail->setSubject($this->translate($emailParams['subject']));
        $mail->send($transport);
    }      

}