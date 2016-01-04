<?php
/**
 * Module based ACL loader.
 * Obtain this object using HCMS_Acl_Loader::getInstance()
 *
 * To get acl object call getAcl
 * To check if current user is allowed for permission go with:
 *
 * $aclLoader = HZ_Acl_Loader::getInstance()
 * $aclLoader->getAcl()->isAllowed($aclLoader->getProfileRoleId(),$aclLoader->getResourceIdFromString('module/controller'),'privilege')
 *
 * or shorter:
 *
 * $aclLoader->isCurrentRoleAllowed('module/controller', 'privilege');
 *
 * where in most cases privilege = action
 *
 *
 * @package HCMS
 * @subpackage Acl
 * @copyright Horisen
 * @author milan
 */
class HCMS_Acl_Loader {

    /**
     * Singleton object
     * 
     * @var HCMS_Acl_Loader
     */
    protected static $_instance = null;

    /**
     * Zend ACL Object
     * @var Zend_Acl
     */
    protected $_acl = null;

    /**
     *
     * @var string
     */
    protected $_currentRoleCode = '';

    /**
     * constructor
     */
    function __construct() {
        $this->_acl = new Zend_Acl();
    }

    /**
     * Singleton implementation
     *
     * @return HCMS_Acl_Loader
     */
    public static function getInstance() {
        if(self::$_instance === null) {
            self::$_instance = new HCMS_Acl_Loader();
        }
        return self::$_instance;
    }

    /**
     * Get Acl Object
     * 
     * @return Zend_Acl
     */
    public function getAcl(){
       return $this->_acl;
    }

    public function load(){
        $this->_loadRoles();
        $this->_loadResources();
        $this->_loadPermissions();
        Zend_Registry::set('acl', $this->_acl);
        return $this;
    }

    /**
     * Set current user role
     *
     * @param string $role
     */
    public function setCurrentRoleCode($role){
        $this->_currentRoleCode = $role;
        return $this;
    }

    /**
     * Get current user role
     * 
     * @return string
     */
    public function getCurrentRoleCode(){
        return  $this->_currentRoleCode;
    }

    /**
     * Get role code by id
     * 
     * @param int $roleId
     * @return string
     */
    public function getRoleCode($roleId){
        return 'role-' . $roleId;
    }


    protected function _loadRoles(){
        $roles = Auth_Model_RoleMapper::getInstance()->fetchAll(array(), array('r.parent_id ASC'));
        /* @var $role Auth_Model_Role */
        foreach ($roles as $role) {
            if($role->get_parent_id() > 0){
                $this->_acl->addRole($this->getRoleCode($role->get_id()), $this->getRoleCode($role->get_parent_id()));
            }
            else{
                $this->_acl->addRole($this->getRoleCode($role->get_id()));
            }            
        }
    }

    protected function _loadResources(){
        $resources = Auth_Model_ResourceMapper::getInstance()->fetchAll(array(), array('r.parent_id ASC'));
        /* @var $resource Auth_Model_Resource */
        foreach ($resources as $resource) {
            if($resource->get_parent_id() > 0){
                $this->_acl->addResource($resource->get_code(), $resources[$role->get_parent_id()]);
            }
            else{
                $this->_acl->addResource($resource->get_code());
            }

        }
    }

    protected function _loadPermissions(){
        $acls = Auth_Model_AclMapper::getInstance()->fetchAll(array());
        /* @var $acl Auth_Model_Acl */
        foreach ($acls as $acl) {
            if($acl->get_allowed() == 'yes'){
                $this->_acl->allow($this->getRoleCode($acl->get_role_id()), $acl->get_resource_code(), $acl->get_privilege_code());
            }
            else{
                $this->_acl->deny($this->getRoleCode($acl->get_role_id()), $acl->get_resource_code(), $acl->get_privilege_code());
            }
        }
    }

}
