<?php

/**
 * Statistics controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Cms_StatisticsController extends HCMS_Controller_Action_Admin {
    
    protected $_statistics;
    protected  $_metricsDesc = array(
            'ga:visits'             => array('name'=>'Visits','type' => 'abs'),
            'ga:visitors'           => array('name'=>'Visitors','type' => 'abs'),
            'ga:pageviews'          => array('name'=>'Pageviews','type' => 'abs'),
            'ga:avgTimeOnSite'      => array('name'=>'Avg. time on site','type' => 'avg'),
            'ga:visitBounceRate'    => array('name'=>'Avg. bounce rate','type' => 'per'),
            'ga:percentNewVisits'   => array('name'=>'Percent new visits','type' => 'per')
    );

    protected $_metricsValues = array();

    /**
     * Organize data per metrics not per date
     */
    protected function _repackMetricsData(){
        foreach ($this->_statistics as $date => $values) {
            foreach ($values as $metrics => $value) {
                if(!isset ($this->_metricsValues[$metrics])){
                    $this->_metricsValues[$metrics] = array(
                        'array' => array(),
                        'total' => 0,
                        'avg'   => 0,
                        'text'  => ''
                    );
                }
                $this->_metricsValues[$metrics]['array'][] = (int)$value;
                $this->_metricsValues[$metrics]['total'] += (double)$value;
                $this->_metricsValues[$metrics]['avg'] = $this->_metricsValues[$metrics]['total'] / count($this->_metricsValues[$metrics]['array']);
                switch ($this->_metricsDesc[$metrics]['type']) {
                    case 'abs':
                        $this->_metricsValues[$metrics]['text'] = $this->_metricsValues[$metrics]['total'];
                        break;
                    case 'avg':
                        $this->_metricsValues[$metrics]['text'] = round($this->_metricsValues[$metrics]['avg'],2);
                        break;
                    case 'per':
                        $this->_metricsValues[$metrics]['text'] = round($this->_metricsValues[$metrics]['avg'],2) . "%";
                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function widgetAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getHelper('layout')->disableLayout();
        }

        $gaSettings = $this->_application->get_settings('ga');
        //check ga settings
        if( !isset ($gaSettings) || !isset ($gaSettings['email']) || $gaSettings['email'] == ''
            || !isset ($gaSettings['password']) || $gaSettings['password'] == ''
            || !isset ($gaSettings['account_id']) || $gaSettings['account_id'] == ''
        ){
            return;
        }

        $today = Zend_Date::now();
        $toDate = clone $today->subDay(1);
        $fromDate = clone $today->subDay(6);
        
        $this->_statistics = Cms_Model_GAMapper::getInstance()->fetchByDate($gaSettings['email'], $gaSettings['password'], $gaSettings['account_id'],
                    $fromDate->get("yyyy-MM-dd"),
                    $toDate->get("yyyy-MM-dd"));
        //sparklines data
        $this->_repackMetricsData();
        $this->view->statistics = $this->_statistics;
        $this->view->metricsValues = $this->_metricsValues;
        $this->view->metricsDesc = $this->_metricsDesc;
        $this->view->fromDate = $fromDate;
        $this->view->toDate = $toDate;
    }

}