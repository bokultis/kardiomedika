<?php

class BootstrapCli extends HCLI_BootstrapCli
{
    /**
     * init register logger
     */
    protected function _initRegisterLogger() {
        $this->bootstrap('Log');

        if (!$this->hasPluginResource('Log')) {
            throw new Zend_Exception('Log not enabled in config.ini');
        }

        $logger = $this->getResource('Log');
        assert($logger != null);
        Zend_Registry::set('Zend_Log', $logger);
    }

    /**
     * Add databases to the registry
     *
     * @return void
     */
    public function _initRegistry()
    {
        //save dbs to registry
        $this->bootstrap('db');
        $resDb = $this->getPluginResource('db');
        Zend_Registry::set('db', $resDb->getDbAdapter());
        $cacheManager = $this->getPluginResource('cachemanager');
        if(isset($cacheManager)){
            Zend_Registry::set('cachemanager', $cacheManager->getCacheManager());
        }        
        //db profiler - uncomment to enable and install firephp for firefox
        /*$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
        // Attach the profiler to your db adapter
        $resDb->getDbAdapter()->setProfiler($profiler);*/
    }
}