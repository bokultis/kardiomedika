<?php
/**
 * User Mapper
 *
 * @package Auth
 * @subpackage Models
 * @copyright Horisen
 * @author marko
 */
class Auth_Model_UserMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Auth_Model_UserMapper
     */
    protected static $_instance = null;

    /**
     *
     * @var Auth_Model_DbTable_User
     */
    protected $_dbTable;

    protected static $_translatedFields = array('title','content');

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Auth_Model_DbTable_User();
        $this->_dbTableUserHistoryPassword = new Auth_Model_DbTable_UserHistoryPassword();
    }

    /**
     * get instance
     *
     *
     * @return Auth_Model_UserMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Find and populate entity by id
     *
     * @param string $id
     * @param Auth_Model_User $user
     * @return boolean
     */
    public function find($id, Auth_Model_User $user) {
        
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->where("id = ?", $id);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $user);
        return true;
    }

    /**
     * Find and populate entity by username
     *
     * @param string $username
     * @param Auth_Model_User $user
     * @return boolean
     */
    public function findByUsername($username, Auth_Model_User $user) {
       
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select ->where("username = ?", $username);
        $resultSet = $this->_dbTable->fetchAll($select);
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $user);
        return true;
    }

    /**
     * Find and populate entity by username or email
     * 
     * @param string $auth
     * @param Auth_Model_User $user
     * @return boolean
     */
    public function findByCredentials($auth, Auth_Model_User $user){
        
        /* @var $select Zend_Db_Select */
        $select = $this->_dbTable->select();
        if(!filter_var($auth, FILTER_VALIDATE_EMAIL) === false){
            $select ->where("email = ?", $auth);            
        }else{           
            $select ->where("username = ?", $auth);
        }
        $resultSet = $this->_dbTable->fetchAll($select);
        
        if (0 == count($resultSet)) {
            return false;
        }
        $row = $resultSet->current();
        $this->_rowToEntity($row->toArray(), $user);
        return true;
    }

    /**
     * Find all user
     * @param array $criteria
     * @param array $orderBy
     * @param array $paging
     * @return array 
     */
    public function fetchAll($criteria = array(), $orderBy = array(), &$paging = null) {
        /* @var $select Zend_Db_Select*/
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('u'=>'auth_user'),array('u.*'));
   
        if(isset ($criteria['search_filter'])){
            $searchString =  '%' . $criteria['search_filter'] . '%';            
            $select->where('u.username LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?',$searchString);
        }
        
        if(isset ($criteria['username'])){
            $searchString =  '%' . $criteria['username'] . '%';            
            $select->where(' u.username LIKE ? ',$searchString);
        }
        
        if(isset ($criteria['firstLastName'])){
            $searchString =  '%' . $criteria['firstLastName'] . '%';            
            $select->where(' u.first_name LIKE ? OR u.last_name LIKE ? ',$searchString);
        }
        
        if(isset ($criteria['email'])){
            $searchString =  '%' . $criteria['email'] . '%';            
            $select->where('u.email LIKE ? ',$searchString);
        }
        
        if(isset ($criteria['status'])){
            $searchString =   $criteria['status'] ;            
            $select->where('u.status = ? ',$searchString);
        }
        
        if(isset ($criteria['deleted'])){
            $searchString =   $criteria['deleted'] ;            
            $select->where('u.deleted = ? ',$searchString);
        }
        
        if(isset ($criteria['role_id'])){
            $searchString =   $criteria['role_id'] ;            
            $select->where('u.role_id = ? ',$searchString);
        }
        
        if(isset ($criteria['password_reset'])){             
            $select->where(' u.password_reset = ? ',$criteria['password_reset']);
        }

        if(is_array($orderBy) && count($orderBy) > 0 ){
            $select->order($orderBy);
        }
//        echo $select->__toString();die();
        // init paginator
        if($paging != null){
            $resultSet = $this->_getPagingRows($paging, $select);
        }
        else{
            $resultSet = $this->_dbTable->fetchAll($select);
        }

        $users = array();
        if (0 == count($resultSet)) {
            return $users;
        }

        foreach ($resultSet as $row) {
            $user = new Auth_Model_User();
            $this->_rowToEntity($row->toArray(), $user);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Save entity
     *
     * @param Auth_Model_User $user
     * @param string $language
     */
    public function save(Auth_Model_User $user) {
        $data = array();
        $this->_entityToRow($user, $data);
        $id = $user->get_id();
        if (!isset ($id) || $id <= 0) {
            $date =  new Zend_Date();
            $data['created'] = $date->toString('yyyy-MM-dd HH:mm:ss');
            unset($data['id']);
            $id = $this->_dbTable->insert($data);
            $user->set_id($id);
        } else {
            $this->_dbTable->update($data, array('id = ?' => $id));
        }        
    }

    /**
     * Update user logged
     *
     * @param Auth_Model_User $user
     */
    public function updateUserLogged(Auth_Model_User $user) {        
        $id = $user->get_id();
        if (!isset ($id) || $id <= 0) {
            return false;
        }
        $data = array();
        $date =  new Zend_Date();
        $data['logged'] = $date->toString('yyyy-MM-dd HH:mm:ss');
        $this->_dbTable->update($data, array('id = ?' => $id));
    }
    
    /**
     * Set or delete password reset string
     * 
     * @param Auth_Model_User $user
     * @param string $string
     */
    public function updateUserResetPassword(Auth_Model_User $user, $string){
        $id = $user->get_id();
        if(!isset($id) || $id <= 0){
            return false;
        }
        $data = array(); 
        $data['password_reset'] = $string;
        $this->_dbTable->update($data, array('id = ?' => $id));
    }
    
    protected function _rowToEntity(array $row, Auth_Model_User $user){
        $row['data'] = $this->_getJsonData($row['data']);
        $user->setOptions($row);
    }

    protected function _entityToRow(Auth_Model_User $user,array &$row){
        $this->_populateDataArr($row, $user, array('id','role_id','username',
            'password', 'password_reset', 'first_name', 'last_name', 'email', 'status', 'lang', 'image_path', 'data', 'created', 'logged',"attempt_login","attempt_login_dt", "changed_password_dt"));
        (isset($row['password']))? ($row['password'] = md5($row['password']) ): '';
        (isset($row['data']))?($row['data'] = json_encode($row['data']) ): '';
    }
    
    /**
     * Delete data
     *
     * @param int $id
     * @return int|bool
     */
    public function softDelete(Auth_Model_User $user){
        
        $data['status'] = 'blocked';
        $data['deleted'] = 'yes';
        $result = $this->_dbTable->update($data, array('id = ?' => $user->get_id()));
               
        return ($result > 0);
        
    }
    
    /**
     * Delete data
     *
     * @param int $id
     * @return int|bool
     */
    public function delete(Auth_Model_User $user){
        
        $result = $this->_dbTable->getAdapter()->delete('auth_user', array(
            'id = ?'   => $user->get_id()
        ));
        
        return ($result > 0);
        
    }

    public function saveHistoryUserPassword(Auth_Model_User $user){
        
        $data = array();
        $this->_populateDataArr($data, $user, array('id','password'));
        
        $data["user_id"] = $data["id"];
        unset($data["id"]);
        
        $date = new Zend_Date();
        $data['last_changed_date_password'] = $date->toString('yyyy-MM-dd HH:mm:ss');        
        
        $this->_dbTableUserHistoryPassword->insert($data);
}
    
    public function checkPassedUserPaswwords($user_id, $new_password){
        
        $select = $this->_dbTable->select();
        $select->setIntegrityCheck(false)
                ->from(array('a'=>'auth_user_history_password'),array('a.*'));        
        $select->where('a.user_id = ?', $user_id);
        $select->order("a.last_changed_date_password DESC");
        $select->limit("13");
        //die($select->__toString());
        $passedPasswords = $this->_dbTable->fetchAll($select);
        
        $foundPassword = false;
        
        foreach($passedPasswords as $passedPassword){
            if($passedPassword["password"] == md5($new_password)) {
                $foundPassword = true;        
                break;
            }
        }
        
        if ($foundPassword) {
            return true;
        }else{
            return false;
        }
    }
}