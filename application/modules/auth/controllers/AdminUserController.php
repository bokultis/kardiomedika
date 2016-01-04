<?php

/**
 * Admin User controller
 *
 * @package Auth
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Auth_AdminUserController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;
    
    protected $_authPrivilege = 'master';
    
    public function init(){
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
    }

    /**
     * List users
     */
    public function userAction() {
        $roles = Auth_Model_RoleMapper::getInstance()->fetchAll(array());
        $this->view->roles = $roles;
    }

    /**
     * Ajax listing of users
     */
    public function userListAction() {
        //criteria
        $criteria = array();
        
        if (null != $this->_getParam('username')) {
            $criteria['username'] = $this->_getParam('username');
        }
        if (null != $this->_getParam('firstLastName')) {
            $criteria['firstLastName'] = $this->_getParam('firstLastName');
        }
        if (null != $this->_getParam('email')) {
            $criteria['email'] = $this->_getParam('email');
        }
        if (null != $this->_getParam('status')) {
            $criteria['status'] = $this->_getParam('status');
        }
        if (null != $this->_getParam('role_id')) {
            $criteria['role_id'] = $this->_getParam('role_id');
        }
        
        if(null != $this->_getParam('searchFilter')){
            $criteria['search_filter'] = $this->_getParam('searchFilter');
        }
        
        $criteria['deleted'] = 'no';
        
        //order
        $order = $this->_request->getParam('order');
        if (isset($order)) {
            $order = array($order);
        } else {
            $order = array("u.username ASC");
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
        $records = Auth_Model_UserMapper::getInstance()->fetchAll($criteria, $order, $paging);
        $data = array(
            'total' => $paging['total'],
            'page' => $paging['page'],
            'records' => $paging['records'],
            'perPage' => $paging['perPage'],
            'rows' => array()
        );
//        print_r($this->_filePaths);die;
        /* @var $record Cms_Model_Page */
        foreach ($records as $record) {
            $image_path = (file_exists($this->_filePaths['real']."/".$record->get_image_path()))?$record->get_image_path():"";
            $data['rows'][] = array(
                'id' => $record->get_id(),
                'username' => $record->get_username(),
                'first_name' => $record->get_first_name(),
                'last_name' => $record->get_last_name(),
                'email' => $record->get_email(),
                'status' => $record->get_status(),
                'image_path' => $image_path
            );
        }

        $this->_helper->json->sendJson($data);
    }

    public function userEditAction(){
        $data = $this->getRequest()->getPost('data');
        $id = $this->_getParam('id');

        $aclLoader = HCMS_Acl_Loader::getInstance();
        //check permission
        if($aclLoader->getAcl()->isAllowed($aclLoader->getCurrentRoleCode(), "admin", "master")){
           $this->view->isAdminLogged = true;     
           $data["isAdminLogged"] = true;
        }
        else{
            $this->view->isAdminLogged = false; 
            $data["isAdminLogged"] = false;
        }
        
        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'user-edit')), $this->translate('Action canceled'));
        }        
       
        //create form object
        $form = new Auth_Form_User($data);
        //postback - save?
        if ($this->_formHelper->isSave()) { 
            //check if valid
            if($form->isValid()) {         
                $values = $form->getValues();
                //create entity object from submitted values, and save
                $user = new Auth_Model_User($values);  
                
                $date = new Zend_Date();
                $user->set_changed_password_dt( $date->toString('yyyy-MM-dd HH:mm:ss'));
                
                if(isset ($id) && $id > 0){
                    if(isset($values['new_password']) && $values['new_password'] != ''){
                        $user->set_password($values['new_password']);
                    }
                    $this->savePassHistory($id);
                }
                
                Auth_Model_UserMapper::getInstance()->save($user);                
                
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'user-edit')), $this->translate('User saved.'));
            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }//first run of the form - grab data from mapper
        elseif(!$this->_formHelper->getRequest()->isPost()) {
        
            //edit action
            if(isset ($id) && $id > 0) {
                $user = new Auth_Model_User();
                if(!Auth_Model_UserMapper::getInstance()->find($id, $user)){
                    throw new Exception("User not found");
                }
                //fetch data
                $data = $user->toArray();
            }
        }
        
        $criteria = array();
        $roles = Auth_Model_RoleMapper::getInstance()->fetchAll($criteria);
        $languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->roles = $roles;
        $this->view->languages = $languages;
        $this->view->data = $data;
        //die(print_R($data));
    
    }
    
    public function userDeleteAction(){
        $id = $this->_getParam('id');

        $user = new Auth_Model_User();
        if(!Auth_Model_UserMapper::getInstance()->find($id, $user)){
            return $this->_formHelper->returnError($this->translate('User not found.'));
        }
        Auth_Model_UserMapper::getInstance()->softDelete($user);
        
        //save done, return success
        return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'userDelete')), $this->translate('User deleted.'));
    }
    
    private function savePassHistory($id){
        $oldUser = new Auth_Model_User();
        if(!Auth_Model_UserMapper::getInstance()->find($id, $oldUser)){
            throw new Exception("User not found");
        }

        Auth_Model_UserMapper::getInstance()->getInstance()->saveHistoryUserPassword($oldUser);    
    }
            
}
