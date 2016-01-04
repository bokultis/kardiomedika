<?php

/**
 * Controller action helper to spawn new process
 *
 * @package HCLI
 * @subpackage Controller
 * @author milan
 *
 */
class HCLI_Controller_Action_Helper_Spawn extends Zend_Controller_Action_Helper_Abstract {

    protected $_scriptPath = null;
    protected $_defaultScriptPath = null;

    public function setScriptPath($script = null) {
        if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32') {
            $script = str_replace('/', '\\', $script);
        }
        $this->_scriptPath = $script;
        return $this;
    }

    public function setDefaultScriptPath($script) {
        if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32') {
            $script = str_replace('/', '\\', $script);
        }
        $this->_defaultScriptPath = $script;
        return $this;
    }

    public function direct(array $parameters = null, $controller = null, $action = null, $module = null) {
        if (is_null($parameters)) {
            $parameters = array();
        } else {
            foreach ($parameters as $key => $value) {
                $parameters[$key] = escapeshellarg($value);
            }
        }
        if ($module) {
            $parameters['-m'] = escapeshellarg(HCLI_Controller_Util::encode($module));
        }
        if ($controller) {
            $parameters['-c'] = escapeshellarg(HCLI_Controller_Util::encode($controller));
        }
        if ($action) {
            $parameters['-a'] = escapeshellarg($action);
        }
        $this->_spawnProcess($parameters);
        $this->_scriptPath = null; // reset
    }

    protected function _spawnProcess(array $args) {
        if (is_null($this->_scriptPath)) {
            $script = $this->_defaultScriptPath;
        } else {
            $script = $this->_scriptPath;
        }
        $command = 'php ' . $script;
        foreach ($args as $key => $value) {
            $command .= ' ' . $key . ' ' . $value;
        }
        if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32') {
            $pcommand = 'start /b ' . $command;
        } else {
            $pcommand = $command . ' > /dev/null &';
        }
        if(Zend_Registry::isRegistered('Zend_Log')){
            /* @var $logger Zend_Log */
            $logger = Zend_Registry::get('Zend_Log');
            $logger->log("Spawning with command $pcommand", Zend_Log::INFO);
        }
        pclose(popen($pcommand, 'r'));
    }

}
