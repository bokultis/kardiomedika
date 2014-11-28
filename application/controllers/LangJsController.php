<?php

class LangJsController extends Zend_Controller_Action {

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @param  mixed    $extras    Extra information to log in event
     * @return void
     * @throws Zend_Log_Exception
     */
    protected function _log($message, $priority, $extras = null){
        $logger = null;

        if (Zend_Registry::isRegistered('Zend_Log')) {
            $logger = Zend_Registry::get('Zend_Log');
        }
        if(isset ($logger)){
            $logger->log($message, $priority, $extras);
        }
    }

    public function indexAction() {
        $expires= 60 * 60 * 24 * 14;
        header('Pragma: public');
        header('Cache-Control: max-age=' . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        header('Content-type: text/javascript');

        $this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender();
        $cacheTranslation = HCMS_Cache::getInstance()->getObjectCache(Application_Model_TranslateMapper::getInstance());
        $this->view->translations = $cacheTranslation->__call('load', array($this->_request->getParam('lang'),'js',true));
    }

    public function cacheCleanTranslationAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        HCMS_Cache::getInstance()->getObjectCache(Application_Model_TranslateMapper::getInstance())->clean();
        $this->_log("Translation cache cleaned explicitly.", Zend_Log::INFO);

        $this->_helper->json(array("success"=>true));
    }

    public function cacheCleanGlobalAction(){
        HCMS_Cache::getInstance()->getObjectCache(Application_Model_TranslateMapper::getInstance())->clean();
        HCMS_Cache::getInstance()->getCoreCache()->clean();
        $this->_log("All caches cleaned explicitly.", Zend_Log::INFO);

        $this->_helper->json(array("success"=>true));
    }
}

