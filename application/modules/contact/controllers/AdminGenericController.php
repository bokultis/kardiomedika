<?php

/**
 * Admin content generic controller - supports multiple forms
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan, miljan, boris
 */
class Contact_AdminGenericController extends HCMS_Controller_Action_Admin {

    /**
     *
     * @var HCMS_Controller_Action_Helper_AjaxForm
     */
    protected $_formHelper = null;
    protected $_formId = 'contact';
    protected $_formParams = array();
    protected $_fields = array();
    protected $_globalSettings = array();
    protected $_languages = array();
    protected $_columns = array();
    protected $_fakeTypes = array('static', 'honeypot', 'captcha');

    protected function _loadParams()
    {
        $this->_globalSettings = HCMS_Utils::loadThemeConfig('config.php', 'contact');
        $fieldTypes = HCMS_Utils::loadThemeConfig('types.php', 'contact');
        if ($this->_request->getParam('form_id')) {
            $this->_formId = $this->_request->getParam('form_id');
        }
        if (!isset($this->_globalSettings['forms'][$this->_formId])) {
            throw new Exception("Form not found");
        }
        $this->_formParams = $this->_globalSettings['forms'][$this->_formId];
        $this->_fields = Contact_Form_Generic::getFieldsArr($fieldTypes, $this->_formParams['fields']);

        $this->_entityClassName = $this->getEntityClassName();
        $this->_mapperClassName = $this->getMapperClassName();
        $this->_columns = $this->getColumns();
        $this->view->columns = $this->_columns;
        $this->view->formId = $this->_formId;
    }

    public function init()
    {
        parent::init();
        $this->_formHelper = $this->getHelper('ajaxForm');
        $this->_languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->_loadParams();
    }

    public function indexAction()
    {
        $this->view->languages = Application_Model_TranslateMapper::getInstance()->getLanguages();
        $this->view->lang = (string) Zend_Registry::get('Zend_Locale')->getLanguage();
    }

    /**
     * Get entity class name
     * 
     * @return string
     */
    protected function getEntityClassName()
    {
        return isset($this->_formParams['entity_class']) ? $this->_formParams['entity_class'] : 'Contact_Model_Contact';
    }

    /**
     * Get mapper class name
     * 
     * @return string
     */
    protected function getMapperClassName() {
        return isset($this->_formParams['mapper_class']) ? $this->_formParams['mapper_class'] : 'Contact_Model_ContactMapper';
    }

    protected function getMapper()
    {
        $mapperClassName = $this->getMapperClassName();
        return $mapperClassName::getInstance();
    }
    
    protected function getColumns()
    {
        $columns = array(
            'id'    => array(
                'name'  => 'ID'
            ),
            'posted' => array(
                'name'  => 'Posted'
            )
        );
        foreach ($this->_fields as $fieldId => $field) {
            if(in_array($field['type'], $this->_fakeTypes)){
                continue;
            }
            $columns[$fieldId] = array(
                'name'  => $field['name'],
                'type'  => $field['type']
            );
        }
        return $columns;        
    }
    
    protected function getRowData($record, $columns)
    {        
        $result = array();
        foreach ($columns as $columnId => $column) {
            if($columnId == 'posted'){
                $result[$columnId] = HCMS_Utils_Time::timeMysql2Local($record->get_posted());
                continue;
            }
            if($columnId == 'language'){
                $result[$columnId] = $this->_languages[$record->get_language()]['name'];
                continue;
            }
            if($columnId == 'gender'){
                $result[$columnId] = $this->translate($record->get_gender());
                continue;
            }              
            $methodName = 'get_' . $columnId;
            if(!method_exists($record, $methodName)){
                continue;
            }
            $result[$columnId] = $record->$methodName();
            if(isset($column['type']) && in_array($column['type'], array('textarea', 'message'))){
                $result[$columnId . '_short'] = $this->view->abbreviate($record->$methodName(), 150);
            }
        }
        return $result;
    }
    
    protected function populateCriteria(&$criteria, &$orderBy, &$paging)
    {
        $page = $this->_request->getParam('page');
        $form_id = $this->_request->getParam('form_id');
        $perPage = $this->_request->getParam('perPage');
        $langFilter = $this->_request->getParam('langFilter');
        $searchFilter = $this->_request->getParam('searchFilter');
        $fromFilter = $this->_request->getParam('fromFilter');
        $toFilter = $this->_request->getParam('toFilter');
        $orderFilter = $this->_request->getParam('order');

        if (isset($fromFilter) && !empty($fromFilter))
            $fromFilter = HCMS_Utils_Date::dateLocalToCustom($fromFilter);
        if (isset($toFilter) && !empty($toFilter))
            $toFilter = HCMS_Utils_Date::dateLocalToCustom($toFilter);

        if (isset($orderFilter) && !empty($orderFilter)) {
            $orderBy = array($orderFilter);
        } else {
            $orderBy = array('c.posted DESC');
        }
        if (!isset($page) || $page < 1) {
            $page = 1;
        }
        if (!isset($perPage) || $perPage < 1 || $perPage > 100) {
            $perPage = 100;
        }
        $paging = array('page' => $page,
            'perPage' => $perPage);
        $criteria = array(
            'application_id' => $this->_applicationId
        );
        if (isset($searchFilter) && $searchFilter != "") {
            $criteria['search_filter'] = $searchFilter;
        }
        
        if (isset($form_id) && $form_id != "") {
            $criteria['form_id'] = $form_id;
        }

        if (isset($langFilter) && $langFilter != "") {
            $criteria['lang_filter'] = $langFilter;
        }

        if (isset($fromFilter) && $fromFilter != "") {
            $criteria['from_filter'] = $fromFilter;
        }

        if (isset($toFilter) && $toFilter != "") {
            $criteria['to_filter'] = $toFilter;
        }
        return;
    }

    /**
     * List action
     */
    public function contactListAction()
    {
        $criteria = array(); $orderBy = array(); $paging = array();
        $this->populateCriteria($criteria, $orderBy, $paging);
        $records = $this->getMapper()->fetchAll($criteria, $orderBy, $paging);

        $data = array(
            'total' => $paging['total'],
            'page' => $paging['page'],
            'records' => $paging['records'],
            'perPage' => $paging['perPage'],
            'rows' => array()
        );

        /* @var $record Contact_Model_Contact */
        foreach ($records as $record) {
            $data['rows'][] = $this->getRowData($record, $this->_columns);
        }
        $this->_helper->json->sendJson($data);
    }

    /**
     * Export action
     */
    public function exportAction() {
        $criteria = array(); $orderBy = array(); $paging = array();
        $this->populateCriteria($criteria, $orderBy, $paging);

        //header
        $header = array();
        foreach ($this->_columns as $columnId => $column) {
            $header[$columnId] = $this->translate($column['name']);
        }

        //read data to export
        $contacts = array();
        $records = $this->getMapper()->fetchAll($criteria, $orderBy);
        foreach ($records as $record) {
            $contacts[] = $this->getRowData($record, $this->_columns);
        }        

        //get exported object
        $objPHPExcel = $this->getMapper()->exportToExcel($this->_applicationId, $header, $contacts, $this);
        if ($objPHPExcel != null) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            //disable layout
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            // rename sheet
            $objPHPExcel->getActiveSheet()->setTitle($this->translate($this->_application->get_name()));
            $fileName = $this->_application->get_name() . "-contact" . "-" . Zend_Date::now()->toString('d-MMM-Y') . ".xls";
            // redirect output to client browser
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            //create excel writer
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            // output to browser
            $objWriter->save('php://output');
            exit();
        } else {
            $this->getHelper('flashMessenger')->addMessage(array("err" => $this->translate("Error occurred while exporting!")));
            $this->_redirect($this->view->url(array('action' => 'contact')));
        }
    }

    public function contactDeleteAction() {
        $id = $this->_getParam('contact_id');
        $entityClassName = $this->getEntityClassName();
        $contact = new $entityClassName();
        $mapper = $this->getMapper();
        if (!$mapper->find($id, $contact)) {
            return $this->_formHelper->returnError($this->translate('Contact not found.'));
        }
        $mapper->delete($contact);
        return $this->_formHelper->returnSuccess($this->view->url(array('action' => 'index')), $this->translate('Contact deleted.'));
    }

}
