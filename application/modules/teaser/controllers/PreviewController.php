<?php

/**
 * Preview controller
 *
 * @package Teaser
 * @subpackage Controllers
 * @copyright Horisen
 * @author milan
 */
class Teaser_PreviewController extends HCMS_Controller_Action_Cms {    
    public function indexAction(){
        $teaserId = $this->_getParam('preview_teaser_id');
        $previewDTIso = $this->_request->getParam('preview_dt');
        if(!isset($teaserId)){
            throw new Exception('Slider not defined');
        }
        if(!isset($previewDTIso)){
            throw new Exception('Date/time not defined');
        }        
        $teaser = new Teaser_Model_Teaser();
        if(!Teaser_Model_TeaserMapper::getInstance()->find($teaserId, $teaser)){
            throw new Exception('Slider not found');
        }

        echo $this->view->renderTeaser2($teaser->get_box_code(), null, CURR_LANG, array(
            'preview_teaser_id' => $teaserId,
            'active'            => 'yes',
            'preview_dt'        => HCMS_Utils_Date::dateLocalToIso($previewDTIso)
        ));
        $this->_helper->viewRenderer->setNoRender(true);
    }     
}