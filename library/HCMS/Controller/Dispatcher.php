<?php

/**
 * HCMS Custom dispatcher to look in customization controller
 *
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */
class HCMS_Controller_Dispatcher extends Zend_Controller_Dispatcher_Standard {   
    
    /**
     * Load a controller class
     *
     * Attempts to load the controller class file from
     * {@link getControllerDirectory()}.  If the controller belongs to a
     * module, looks for the module prefix to the controller class.
     *
     * @param string $className
     * @return string Class name loaded
     * @throws Zend_Controller_Dispatcher_Exception if class not loaded
     */
    public function loadClass($className)
    {
        $finalClass  = $className;
        if (($this->_defaultModule != $this->_curModule)
            || $this->getParam('prefixDefaultModule'))
        {
            $finalClass = $this->formatClassName($this->_curModule, $className);
        }
        if (class_exists($finalClass, false)) {
            return $finalClass;
        }
        
        $fileName = $this->classToFilename($className);        
        
        $loadFile = APPLICATION_PATH . '/../customization/modules/' . $this->_curModule . '/controllers/' . $fileName;
        if (Zend_Loader::isReadable($loadFile)) {
            include_once $loadFile;
        } else {            
            $dispatchDir = $this->getDispatchDirectory();
            $loadFile    = $dispatchDir . DIRECTORY_SEPARATOR . $fileName;
            if (Zend_Loader::isReadable($loadFile)) {
                include_once $loadFile;
            } else {
                require_once 'Zend/Controller/Dispatcher/Exception.php';
                throw new Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $loadFile . "'");
            }            
        }

        if (!class_exists($finalClass, false)) {
            require_once 'Zend/Controller/Dispatcher/Exception.php';
            throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $finalClass . '")');
        }

        return $finalClass;
    }
}
