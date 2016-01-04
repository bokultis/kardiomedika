<?php
/**
 * Theme Path view helper - prefixes theme path
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author ljuba
 *
 */
class HCMS_View_Helper_ContentBlock extends Zend_View_Helper_Abstract {

    /**
     * Content block view helper - retrieves a content block with certain code
     *
     * @param string $url_id - url_id of the content block
     * @param strin $lang - current language
     * @return string
     */
    private $_application_id = 1;
    
    private $_type = 'contentblock';

    public function contentBlock($id, $lang = null, $findBy = 'url_id', $type = null) {
        if(!isset($lang)){
            $lang = CURR_LANG;
        }
        if(!isset($type)){
            $type = $this->_type;
        }        
        $block = new Cms_Model_Page();
        if($findBy == 'url_id'){
            $result = Cms_Model_PageMapper::getInstance()->findByUrlId($id , $this->_application_id, $block, $lang, $type);
        }
        elseif($findBy == 'id'){
            $result = Cms_Model_PageMapper::getInstance()->find($id , $block, $lang);
        }        
        else{
            $result = Cms_Model_PageMapper::getInstance()->findByCode($id , $this->_application_id, $block, $lang);
        }
        
        if(!$result){
            return false;
        }
        $pageFormat = $block->get_format();
        //render differently based on format
        switch($pageFormat) {
            case 'html':
                return $this->view->frontAdmin()->renderEditable($block->get_content(), $block);
                break;
            case 'path':
                if(isset($this->view->theme)){
                    $themePath = APPLICATION_PATH . '/../themes/' . $this->view->theme . '/views/cms';
                    //add theme view path
                    $this->view->addScriptPath($themePath);                
    }
                return $this->view->render('page/static/' .$block->get_content());
                break;
}
    }
}