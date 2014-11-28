<?php

class CliController extends HCLI_Controller_Action {

    /**
     * Logger
     * @var Zend_Log
     */
    protected $_logger = null;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
        parent::__construct($request, $response, $invokeArgs);
        $this->_logger = Zend_Registry::get('Zend_Log');
    }

    public function echoAction() {
        $console = $this->getConsoleOptions(
                array('name|n=s' => 'Tell me your name')
        );
        $message = 'Hello ' . $console->getOption("name");
        echo $message, "\n";
        $this->_logger->log($message, Zend_Log::INFO);
        exit(0);
    }

    private function _getSQLFiles($dir,$branch,&$files) {
        if (false !== ($handle = opendir($dir))) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                $ext = pathinfo($entry, PATHINFO_EXTENSION);
                if($ext == 'sql' && strpos($entry, '.tpl.sql') === false) {
                    $files[$branch][] = $entry;
                }
            }
            closedir($handle);
        }
    }

    /* This is the static comparing function: */
    static function cmpFiles($a, $b) {
        $al = strtolower($a);
        $bl = strtolower($b);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    /**
     * Get full file path
     *
     * @param string $file
     * @param string $branch
     * @param string $ext
     * @return string
     */
    private function _getFilePath($file,$dir,$ext = 'sql') {
        if($ext != 'sql') {
            $info = pathinfo($file);
            $file = $info['filename'] . '.' . $ext;
        }
        $fileName = $dir . "/" . $file;
        return $fileName;
    }

    /**
     * Run all sql scripts from defined directories, but also can run PHP files with the same name
     * which return function variable in the form, ex:
     *
     * function upd104(Zend_Db_Adapter_Abstract $db){
     *    $application = new Application_Model_Application();
     *    if(!Application_Model_ApplicationMapper::getInstance()->find(10, $application)){
     *        return false;
     *    }
     *    echo $application->get_name() . "\n";
     *    return true;
     * };
     *
     * return 'upd104';
     *
     *
     *
     * @return mixed
     */
    public function dbupdAction() {
        $console = $this->getConsoleOptions(
                array(
                'simulation|s=s' => 'Just simulate yes|no'
                )
        );

        $simulation = $console->getOption("simulation") != 'no';

        if($simulation) {
            echo "\nRUNNING in SIMULATION mode\n";
        }
        else {
            echo "\nRUNNING in LIVE mode\n";
        }

        $appOptions = $this->getInvokeArg('bootstrap')->getOptions();
        if(!isset ($appOptions['dbupd']) || !isset ($appOptions['dbupd']['dirs'])) {
            echo "Please specify [dbupd.dirs] in cli.ini !\n";
            return;
        }
        $dirs = $appOptions['dbupd']['dirs'];

        $sqlFiles = array();

        //get files
        foreach ($dirs as $branch => $dir) {
            $this->_getSQLFiles($dir, $branch, $sqlFiles);
            usort($sqlFiles[$branch], array("CliController", "cmpFiles"));
        }
        //get existing db updates
        /* @var $db Zend_Db_Adapter_Abstract */
        $db = Zend_Registry::get('db');
        $sql = 'SELECT * FROM upgrade_db_log';
        try {
            $result = $db->fetchAll($sql);
            foreach ($result as $row) {
                $branch = isset ($row['branch'])?$row['branch']:'trunk';
                $file = trim($row['file']);
                $index = array_search($file, $sqlFiles[$branch]);
                if($index !== FALSE) {
                    unset ($sqlFiles[$branch][$index]);
                }
            }
        }catch (Exception $exc) {
            if($exc->getCode() != 42) {
                echo "\n" . $exc->getMessage() . "\n";
                return false;
            }
        }

        //check if any files
        $hasUpdates = false;
        foreach ($sqlFiles as $branch => $files) {
            if(count($files) > 0) {
                $hasUpdates = true;
                break;
            }
        }
        if(!$hasUpdates) {
            echo "No files for update.\n";
            return;
        }

        if($simulation) {
            echo "These files will be execute if you turn off simulation by running with options \"-s no\":\n";
            //get files
            foreach ($sqlFiles as $branch => $files) {
                foreach ($files as $file) {
                    //check if php update exists with the same name
                    if(file_exists($this->_getFilePath($file, $dirs[$branch], 'php'))) {
                        echo "[$file](php) in $branch\n";
                    }
                    else {
                        echo "[$file] in $branch\n";
                    }
                }
            }
            return;
        }
        else {
            $dbConf = $db->getConfig();
            //get files
            foreach ($sqlFiles as $branch => $files) {
                foreach ($files as $file) {
                    //execute php first if exists
                    $phpFile = $this->_getFilePath($file, $dirs[$branch], "php");
                    if(file_exists($phpFile)) {
                        $phpFunction = require $phpFile;
                        //we can continue only if php function returns TRUE
                        if(!$phpFunction($db)) {
                            echo "result: php error\n";
                            continue;
                        }
                    }
                    $cmd = sprintf('mysql --default-character-set=utf8 --host=%s --user=%s --password=%s %s < %s',$dbConf['host'],$dbConf['username'],$dbConf['password'],$dbConf['dbname'],$dirs[$branch] . "/" . $file);
                    echo "\nExecuting [$file]...";
                    //echo "\n$cmd";
                    $lastLine = system($cmd, $retval);
                    echo "result: $retval\n";
                }
            }
        }

        echo "DONE!\n";
    }

}