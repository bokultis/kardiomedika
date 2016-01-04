<?php

/**
 * CMS db routes
 *
 * @package    HCMS
 * @subpackage Controller
 * @copyright  Horisen
 */
class HCMS_Controller_Router_Route_Cms extends Zend_Controller_Router_Route_Abstract {

    protected $_defaults = array();

    /**
     * Prepares the route for mapping.
     *
     * @param array $defaults Defaults for map variables with keys as variable names
     */
    public function __construct($defaults = array()) {
        $this->_defaults = (array) $defaults;
    }

    /**
     * A method to publish the way the route operates.
     *
     * @see Zend/Controller/Router/Rewrite.php:392
     *
     * @return int
     */
    public function getVersion() {
        return 1;
    }

    /**
     * The required functions (required by Interface).
     *
     * @param Zend_Config $config The config object with defaults.
     *
     * @return HCMS_Controller_Router_Route_Cms
     */
    public static function getInstance(Zend_Config $config) {
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($defs);
    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param string $path Path used to match against this routing map
     *
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path) {
        //$this->_log(sprintf("cms route match path: %s",  $path), Zend_Log::DEBUG);        
        $path = trim($path, '/');
        //if singlelang than force lang manually
        $singleLang = Zend_Controller_Front::getInstance()->getParam('singleLang');
        if($singleLang && $singleLang != ''){
            $path = $singleLang . '/' . $path;
        }
        $parts = explode('/', $path);
        //grab lang
        if (count($parts) == 0) {
            $lang = null;
            $findUri = '';
        } else {
            $lang = $parts[0];
            if(Application_Model_TranslateMapper::getInstance()->isLangAvailable($lang)){
                array_shift($parts);
            }
            else{                
                $lang = null;
            }
            $findUri = implode('/', $parts);
        }

        $route = new Cms_Model_Route();
        if (!Cms_Model_RouteMapper::getInstance()->findByUri($findUri, 1, $route, $lang)) {
            //$this->_log(sprintf("cms route path [%s] not found",  $path), Zend_Log::DEBUG);
            return false;
        }

        list($module, $controller, $action) = explode('/', $route->get_path());
        $result = array(
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
            'lang' => $lang
        );
        foreach ($route->get_params() as $paramName => $paramValue) {
            $result[$paramName] = $paramValue;
        }

        return $result;
    }

    /**
     * Assembles a URL path defined by fallback route
     *
     * @param array   $data    An array of variable and value pairs used as parameters
     * @param boolean $reset   Not used (required by interface)
     * @param boolean $encode  Not used (required by interface)
     * @param boolean $partial Not used (required by interface)
     *
     * @return string|false Route path with user submitted parameters
     */
    public function _assembleFallback($data = array(), $reset = false, $encode = false, $partial = false) {
        $result = Zend_Controller_Front::getInstance()->getRouter()->assemble($data, 'default', $reset, $encode);
        $result = ltrim($result, "/");
        return $result;
    }

    /**
     * Assembles a URL path defined by this route
     *
     * @param array   $data    An array of variable and value pairs used as parameters
     * @param boolean $reset   Not used (required by interface)
     * @param boolean $encode  Not used (required by interface)
     * @param boolean $partial Not used (required by interface)
     *
     * @return string|false Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false, $partial = false) {
        $origData = $data;
        //$this->_log(sprintf("cms route asseble data: %s",  json_encode($data)), Zend_Log::DEBUG);
        if (!isset($data['lang'])) {
            if (Zend_Registry::isRegistered('Zend_Locale')) {
                $locale = Zend_Registry::get('Zend_Locale');
                $data['lang'] = $locale->getLanguage();
            } else {
                //fallback to default route
                return $this->_assembleFallback($origData, 'default', $reset, $encode);
            }
        }

        $module = isset($data['module']) ? $data['module'] : $this->getDefault('module');
        $controller = isset($data['controller']) ? $data['controller'] : $this->getDefault('controller');
        $action = isset($data['action']) ? $data['action'] : $this->getDefault('action');

        $path = "$module/$controller/$action";
        $lang = $data['lang'];
        unset($data['module']);
        unset($data['controller']);
        unset($data['action']);
        unset($data['lang']);
        $route = new Cms_Model_Route();
        if (!Cms_Model_RouteMapper::getInstance()->findByPath($path, 1, $route, $data, $lang)) {
            //fallback to default route
            return $this->_assembleFallback($origData, 'default', $reset, $encode);
        }
        $result = array();
        //if singlelang than do not add lang
        $singleLang = Zend_Controller_Front::getInstance()->getParam('singleLang');
        if ((!$singleLang || $singleLang == '') && isset($lang) && $lang != '') {
            $result[] = $lang;
        }
        if (null != $route->get_uri() && '' != $route->get_uri()) {
            $result[] = $route->get_uri();
        }
        return implode("/", $result);
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @param  mixed    $extras    Extra information to log in event
     * @return void
     * @throws Zend_Log_Exception
     */
    protected function _log($message, $priority, $extras = null) {
        /* @var $logger Zend_Log */
        $logger = Zend_Registry::get('Zend_Log');
        $logger->log($message, $priority, $extras);
    }

}