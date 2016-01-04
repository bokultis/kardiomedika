<?php

/**
 * Admin Reset Password Controller
 *
 * @package Auth
 * @subpackage Controllers
 * @copyright Horisen
 * @author ilija
 */
class Auth_AdminResetPasswordController extends HCMS_Controller_Action_Admin {
    
    protected $_formHelper = null;
    
    public function init(){
        $this->_checkAuth = false;
        $this->_formHelper = $this->getHelper('ajaxForm');
        parent::init();
    }
    
    
    public function indexAction(){
        $this->view->layout()->setLayout('login');
        if ($this->getRequest()->isPost()) {  
            $user = new Auth_Model_User();
            $auth = $this->_getParam('auth', "");
            $isExistUser = Auth_Model_UserMapper::getInstance()->getInstance()->findByCredentials($auth, $user);
            if($isExistUser){
                $criteria = array();
                if(!filter_var($auth, FILTER_VALIDATE_EMAIL) === false){
                    $criteria['email'] = $auth;
                }else{           
                    $criteria['username'] = $auth;
                }
           
            $results = Auth_Model_UserMapper::getInstance()->getInstance()->fetchAll($criteria);
            foreach($results as $result){
                $user->set_id($result->get_id());
                $id = $result->get_id();
                $mail = $result->get_email();
            }
            
            $randomString = $this->_generateRandomString(10);
            Auth_Model_UserMapper::getInstance()->getInstance()->updateUserResetPassword($user, $randomString);
            $url =  $this->view->serverUrl().
                    $this->view->url(array('module' => 'auth', 'controller' => 'admin-reset-password', 'action' => 'reset', 'user' => $id, "token" => $randomString), 'default', true);
            
            $this->_sendNotificationEmail(
                    $url,
                    array(
                        "subject"   => $this->_application->get_name() . " - Password reset",
                        "to_emails" => array($mail)
                    ), CURR_LANG);  
            
            return $this->_setResetError("Password reset URL was sent", true);
            }else{
                return $this->_setResetError("User does not exist");
            }
        }
    }
    
    public function resetAction(){
        $this->view->layout()->setLayout('login');
        
        $data = $this->getRequest()->getPost();
        $data['id'] = (int) $this->_getParam('user');
       
        
        $form = new Auth_Form_Reset($data);
        if($this->_formHelper->getRequest()->isPost()){
            if($form->isValid()){
                $user = new Auth_Model_User();
            
                $isExistUser = Auth_Model_UserMapper::getInstance()->getInstance()->find($data['id'], $user);

                if(!$isExistUser){
                    return $this->_setResetError($this->translate("User does not exist"));
                }

                if($user->get_password_reset() != $this->_getParam('token')){
                    return $this->_setResetError($this->translate("Wrong token code"));
                }
                if($data['new_password'] != $data['new_password_confirm']){
                    return $this->_setResetError($this->translate("You need to confirm Password"));
                }
                $this->savePassHistory($data['id']);
                
                $user->set_password($data['new_password']);
                $user->set_password_reset(new Zend_Db_Expr('NULL'));
                //Auth_Model_UserMapper::getInstance()->getInstance()->updateUserResetPassword($user, NULL);
                Auth_Model_UserMapper::getInstance()->save($user);

                $this->view->success = true;
                return true;            
            }else{
                $errorMessages = $form->getMessages();
                $results = array();
               
                if(isset($errorMessages['new_password'])){
                    $results = array_merge($results,$errorMessages['new_password']);
                }
                if(isset($errorMessages['new_password_confirm'])){
                    $results = array_merge($results,$errorMessages['new_password_confirm']);
                }
                $message = "";
                foreach($results as $result){
                    $message .= $result."<br>";
                }
                return $this->_setResetError($message, false);
            }
        }else{
            $id = $this->_getParam('user');
            $token = $this->_getParam('token');
            $tokenExist = Auth_Model_UserMapper::getInstance()->getInstance()->fetchAll(array("password_reset" => $token));
            if($token == "" || $id == "" || sizeof($tokenExist) == 0){
                return $this->_formHelper->returnError($this->translate("Wrong URL"));
            }
            $this->view->id = $id;
        }
        
        /*
        if ($this->getRequest()->isPost()) {  
            die();
            $new_password = $this->_getParam('new_password');
            $new_password_confirm = $this->_getParam('new_password_confirm');
            $data = array(
                'new_password' => $new_password,
                'new_password_confirm' => $new_password_confirm
            );
            
            $form = new Auth_Form_Reset($data);
            if(!$form->isValid()){
                return $this->_setResetError($form->getMessages(), false);
                
            }            
            
            $user = new Auth_Model_User();
            
            $isExistUser = Auth_Model_UserMapper::getInstance()->getInstance()->find($id, $user);
            
            if(!$isExistUser){
                return $this->_setResetError($this->translate("User does not exist"));
            }
            
            if($user->get_password_reset() != $this->_getParam('token')){
                return $this->_setResetError($this->translate("Wrong token code"));
            }
            
            $user->set_password($new_password);
            $user->set_password_reset(NULL);
            Auth_Model_UserMapper::getInstance()->getInstance()->updateUserResetPassword($user, NULL);
            Auth_Model_UserMapper::getInstance()->save($user);
            
            $this->view->success = true;
            return true;            
        } */
        
    }
    
    private function savePassHistory($id){
        $oldUser = new Auth_Model_User();
        if(!Auth_Model_UserMapper::getInstance()->find($id, $oldUser)){
            throw new Exception("User not found");
        }

        Auth_Model_UserMapper::getInstance()->getInstance()->saveHistoryUserPassword($oldUser);    
    }
    
    protected function _setResetError($errorMessage, $success = false){
        if($success){
            $this->view->success = true;
        }else{
            $this->view->success = false;
        }
        if(is_array($errorMessage)){
            $errorMessages = $errorMessage['new_password'];
            $message = "";
            foreach($errorMessages as $key => $value){
                $message .= $value."<br>";
            }
            $errorMessage = $errorMessage['old'];
            Zend_Debug::dump($message);
            $errorMessage = $message;
        }
        $this->view->messages = $this->translate($errorMessage);
    }
    
    protected function _generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
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
    protected function _getEmailBody(Zend_View $emailView, array $emailParams, $templateName, $language) {
        $content = $emailView->render(sprintf($templateName, $language));
        if (!isset($emailParams['layout'])) {
            return $content;
        }
        $emailView->body = $content;
        return $emailView->render(sprintf($emailParams['layout'], $language));
    }
    
    protected function _sendNotificationEmail($url, $emailParams, $language, $template = 'notification.phtml') {
        $emailParams = array_merge($this->_application->get_email_settings(), $emailParams);
        //print_r($emailParams); die();

        $transport = HCMS_Email_TransportFactory::createFactory($emailParams);
        //init view        
        $emailView = new Zend_View();
        $emailView->setScriptPath($this->getFrontController()->getModuleDirectory('admin') . '/views/scripts/email_templates/');
        $mvcView = clone Zend_Layout::getMvcInstance()->getView();
        if (isset($mvcView->theme)) {
            $emailView->addScriptPath(APPLICATION_PATH . '/../themes/' . $mvcView->theme . '/views/auth/email_templates/');
        }
        $message = $this->translate("To reset your password, please click on link: ");
        $message .= '<a href="'.$url.'">Reset your password</a>';
        $emailView->assign(array(
            'application' => $this->_application,
            'message' => $message,
            'lang' => $language,
            'serverUrl' => $this->view->serverUrl(),
            'imagesUrl' => isset($mvcView->theme) ? $this->view->serverUrl() . '/themes/' . $mvcView->theme . '/images/email/' : $this->view->serverUrl() . '/images/email/'
        ));
        
        $body = $this->_getEmailBody($emailView, $emailParams, $template, $language);

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

