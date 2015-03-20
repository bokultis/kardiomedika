<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library')
)));

//composer autoload
require_once APPLICATION_PATH . '/../vendor/autoload.php';

/** Zend_Application */
require_once 'Zend/Application.php';
//require_once 'Facebook/facebook.php';
require_once 'Zend/Cache.php';

// in case require_once is commented in zend library
/*
require_once 'Zend/Config.php';
require_once 'Zend/Cache/Backend.php';
require_once 'Zend/Cache/Core.php';
require_once 'Zend/Cache/Frontend/File.php';
require_once 'Zend/Cache/Backend/Interface.php';
require_once 'Zend/Cache/Backend/ExtendedInterface.php';
require_once 'Zend/Cache/Backend/File.php';
 */
require_once 'Zend/Config/Ini.php';
//require_once 'HCMS/Utils/Profiler.php';
//require_once 'Zend/Registry.php';

//HCMS_Utils_Profiler::checkpoint("start");

$configPath = APPLICATION_PATH . '/configs/application.ini';

//cache
$frontendOptions = array(
    'lifetime'                  => 7200, // cache lifetime of 2 hours,
    'automatic_serialization'   => true,
    'master_files'              => array($configPath)
);
$backendOptions = array(
    'cache_dir' => APPLICATION_PATH . '/../cache/file' // Directory where to put the cache files
);
// getting a Zend_Cacheobject
$cache = Zend_Cache::factory('File', 'File', $frontendOptions, $backendOptions);
//only in production mode use cache
if (APPLICATION_ENV != 'production' || !$cache->test('configuration')) {

    $config = new Zend_Config_Ini($configPath, APPLICATION_ENV);
    $cache->save($config, 'configuration');
}
else{
    $config = $cache->load('configuration');
}

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    $config
);
//HCMS_Utils_Profiler::checkpoint("pre boot");
$application->bootstrap()
            ->run();

//HCMS_Utils_Profiler::checkpoint("post run");