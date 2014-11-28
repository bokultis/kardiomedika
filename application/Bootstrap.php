<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        //$view->placeholder('credits');
        $view->doctype('HTML5');
    }

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
        Zend_Registry::set('cachemanager', $cacheManager->getCacheManager());
        //db profiler - uncomment to enable and install firephp for firefox
        /*$profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
        // Attach the profiler to your db adapter
        $resDb->getDbAdapter()->setProfiler($profiler);*/
    }

    /**
     * Set caching
     *
     * @return void
     */
    public function _initCache()
    {
        $cache = HCMS_Cache::getInstance()->getCoreCache();
        //set cache for table metadata
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        //set cache for locale
        Zend_Locale::setCache($cache);
        //set cache for translate
        Zend_Translate::setCache($cache);
        //plugin loader cache
        $classFileIncCache = APPLICATION_PATH . '/../cache/file/pluginLoaderCache.php';
        if (file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }
        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
    }

}