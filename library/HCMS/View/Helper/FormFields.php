<?php
/**
 * Render generic form fields
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_FormFields {

    private $view;
    
    public function setView($view) {
        $this->view = $view;
    }

    /**
     * Render form fields
     *
     * @param array $fields
     * @param array $params
     * @return string
     */
    public function formFields(array $fields, array $params = array()) {
        $html = '';
        foreach ($fields as $fieldId => $field) {
            $template = isset($field['template'])? $field['template'] : 'input.phtml';
            $html .= $this->view->partial('fields/' . $template, array(
                'formId'    => isset($params['formId']) ? $params['formId'] : $this->view->formId,
                'fieldId'   => $fieldId,
                'field'     => $field,
                'errors'    => isset($params['errors']) ? $params['errors'] : $this->view->errors,
                'data'      => isset($params['data']) ? $params['data'] : $this->view->data
            ));
        }
        return $html;
    }
}