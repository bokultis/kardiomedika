<?php
/**
 * Change language of the current page
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_SwitchLang {

    /**
     *
     * @var Zend_View
     */
    private $_view;
    
    public function setView($view) {
        $this->_view = $view;
    }

    /**
     * Switch lang
     *
     * @return string
     */
    public function switchLang($newLang) {
        $routeParams = Zend_Controller_Front::getInstance()->getRequest()->getUserParams();
        //$routeParams['module'] = 'default';
        //$routeParams['controller'] = 'index';
        //$routeParams['action'] = 'index';
        
        $routeParams['lang'] = $newLang;
        if(isset($routeParams['error_handler'])){
            unset($routeParams['error_handler']);
        }
        if(isset($routeParams['url_id'])){
            $routeParams['url_id'] = htmlspecialchars(urldecode($routeParams['url_id']));
        }
        return $this->_view->url($routeParams,'cms',true);
    }
}