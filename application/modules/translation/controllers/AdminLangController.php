<?php
/**
 * Translation Lang controller
 *
 * @package Translation
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Translation_AdminLangController extends HCMS_Controller_Action_Admin
{
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
    public function indexAction() {
    }

    /**
     * Ajax listing of Language
     */
    public function listAction() {
        //criteria
        $criteria = array();
        
        if(null != $this->_getParam('searchFilter')){
            $criteria['search_filter'] = $this->_getParam('searchFilter');
        }
        
        if (null != $this->_getParam('name')) {
            $criteria['name'] = $this->_getParam('name');
        }
        //order
        $order = $this->_request->getParam('order');
        if (isset($order)) {
            $order = array($order);
        } else {
            $order = array("name ASC");
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
        $records = Translation_Model_LangMapper::getInstance()->fetchAll($criteria, $order, $paging);
       
        $data = array(
            'total' => $paging['total'],
            'page' => $paging['page'],
            'records' => $paging['records'],
            'perPage' => $paging['perPage'],
            'rows' => array()
        );

        /* @var $record Auth_Model_Roles */
        foreach ($records as $record) {
            $data['rows'][] = array(
                'id' => $record->get_id(),
                'code' => $record->get_code(),
                'name' => $record->get_name(),
                'default' => $record->get_default(),
                'front_enabled' => $record->get_front_enabled()
            );
        }

        $this->_helper->json->sendJson($data);
    }

    public function editAction(){
        $data = $this->getRequest()->getPost('data');
        $id = $this->_getParam('id');

        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }        

        //create form object
        $form = new Translation_Form_Lang($data);

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();
                //create entity object from submitted values, and save
                $lang = new Translation_Model_Lang($values);
               
                Translation_Model_LangMapper::getInstance()->save($lang);                
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'index')), $this->translate('Language saved.'));                    

            }
            else {
                //we have errors - return json or continue
                $this->_formHelper->returnError($form->getMessages());
            }
        }
        //first run of the form - grab data from mapper
        elseif(!$this->_formHelper->getRequest()->isPost()) {
            //edit action
            if(isset ($id) && $id > 0) {
                $lang = new Translation_Model_Lang();
                if(!Translation_Model_LangMapper::getInstance()->find($id, $lang)){
                    throw new Exception("Lang not found");
                }
                //fetch data
                $data = $lang->toArray();
                //populate form with data
                $form->setData($data);
            }
        }
        $this->view->data = $data;
    }
    
    /*
     * delete Action
     */
    public function deleteAction(){
        $id = $this->_getParam('id');
        $result = array(
                    "success" => false,
                    "message" => $this->translate("You do not have permission to delete Language.")
                );
        return $this->_helper->json($result);
    }
}

