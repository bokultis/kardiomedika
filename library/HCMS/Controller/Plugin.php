<?php

/**
 * HCMS Controller Plugin to attach Zend_Application_Module_Autoloader
 *
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */
class HCMS_Controller_Plugin extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request){
        $custimizationPath = APPLICATION_PATH . '/../customization/modules';
        if(!is_dir($custimizationPath)){
            return;
        }
        foreach (new DirectoryIterator($custimizationPath) as $fileInfo) {
            if($fileInfo->isDir() && !$fileInfo->isDot() && substr($fileInfo->getFilename(), 0, 1) != '.'){
                //echo $fileInfo->getFilename() . "<br>\n";
                $namespace = ucfirst($fileInfo->getFilename());
                $resourceLoader = new Zend_Application_Module_Autoloader(array(
                    'basePath'  => $custimizationPath . '/' . $fileInfo->getFilename(),
                    'namespace' => $namespace
                ));

                Zend_Loader_Autoloader::getInstance()->pushAutoloader($resourceLoader, $namespace);                 
            }            
        }               
    }        
}
