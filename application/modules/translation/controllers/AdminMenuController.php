<?php
/**
 * Translation Grid controller
 *
 * @package Translation
 * @subpackage Controllers
 * @copyright Horisen
 * @author marko
 */
class Translation_AdminMenuController extends HCMS_Controller_Action_Admin
{
    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;

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
     * Ajax listing of users
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
        $records = Translation_Model_MenuMapper::getInstance()->fetchAll($criteria, $order, $paging);
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
                'name' => $record->get_name()
            );
        }

        $this->_helper->json->sendJson($data);
    }

    public function editAction(){
        $data = $this->getRequest()->getPost('data');
        $submit = $this->getRequest()->getPost('submit');
        $id = $this->_getParam('id');
        $langFilter = $this->_getParam('langFilter');        

        //check if cancel button is pressed
        if($this->_formHelper->isCancel()) {
            //cancel form
            return $this->_formHelper->returnCancel($this->view->url(array('action' => 'index')), $this->translate('Action canceled'));
        }        

        //create form object
        $form = new Translation_Form_Menu($data);

        //postback - save?
        if ($this->_formHelper->isSave()) {            
            //check if valid
            if($form->isValid()) {                
                $values = $form->getValues();
                //create entity object from submitted values, and save
                $menu = new Translation_Model_Menu($values);                
                Translation_Model_MenuMapper::getInstance()->save($menu);                
                //save done, return success
                return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'index')), $this->translate('Menu saved.'));
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
                $menu = new Translation_Model_Menu();
                if(!Translation_Model_MenuMapper::getInstance()->find($id, $menu)){
                    throw new Exception("Menu not found");
                }
                //fetch data
                $data = $menu->toArray();
                //populate form with data
                $form->setData($data);
            }
        }
        $this->view->data = $data;
    }
}

