<?php

trait CliColors
{
    private $foregroundColors = array(
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37'   
    );
    private $backgroundColors = array(
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47'        
    );

    // Returns colored string
    public function getColoredString($string, $foregroundColor = null, $backgroundColor = null)
    {
        $coloredString = "";
        // Check if given foreground color found
        if (isset($this->foregroundColors[$foregroundColor])) {
            $coloredString .= "\033[" . $this->foregroundColors[$foregroundColor] . "m";
        }
        // Check if given background color found
        if (isset($this->backgroundColors[$backgroundColor])) {
            $coloredString .= "\033[" . $this->backgroundColors[$backgroundColor] . "m";
        }
        // Add string and end coloring
        $coloredString .= $string . "\033[0m";
        return $coloredString;
    }
}

class CliController extends HCLI_Controller_Action {
    
    use CliColors;
    
    /**
     * Logger
     * @var Zend_Log
     */
    protected $_logger = null;
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract 
     */
    protected $db = null;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        $this->_logger = Zend_Registry::get('Zend_Log');
        $this->db = Zend_Registry::get('db');        
    }
    
    protected function writeLn($string, $foregroundColor = null, $backgroundColor = null)
    {
        echo "\n" . $this->getColoredString($string, $foregroundColor, $backgroundColor) . "\n";
    }

    /**
     * Run all sql scripts from scripts/dbupdates
     *
     * @return mixed
     */
    public function dbupdAction()
    {
        $console = $this->getConsoleOptions(array(
            'simulation|s=s' => 'Just simulate yes|no'
        ));

        $simulation = $console->getOption("simulation") != 'no';

        if($simulation) {
            $this->writeLn('RUNNING in SIMULATION mode', null, 'yellow');
        } else {            
            $this->writeLn('RUNNING in LIVE mode', null, 'green');
        }                

        $appOptions = $this->getInvokeArg('bootstrap')->getOptions();
        $dir = APPLICATION_PATH . '/../scripts/dbupdates';

        //get files
        $sqlFiles = glob($dir . '/*.sql', GLOB_MARK);
        array_walk($sqlFiles, function(&$item){
            $item = basename($item);
        });
        usort($sqlFiles, function($a, $b){
            $al = strtolower($a);
            $bl = strtolower($b);
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;            
        });

        $sql = 'SELECT * FROM upgrade_db_log';
        try {
            $result = $this->db->fetchAll($sql);
            foreach ($result as $row) {
                $file = trim($row['file']);
                $index = array_search($file, $sqlFiles);
                if($index !== FALSE) {
                    unset ($sqlFiles[$index]);
                }
            }
        }catch (Exception $exc) {
            if($exc->getCode() != 42) {
                $this->writeLn($exc->getMessage(), null, 'red');
                exit(1);
            }
        }

        //check if any files
        if(!count($sqlFiles)) {
            $this->writeLn('No files for update', 'yellow');
            exit(0);
        }
        
        $this->writeLn('Pending SQL files:', null, 'green');
        foreach ($sqlFiles as $sqlFile) {
            $this->writeLn($sqlFile, 'green');
        }        

        if(!$simulation) {
            //get files
            foreach ($sqlFiles as $sqlFile) {
                $this->executeQuery($sqlFile, $dir);
            }
        } else {
            $this->writeLn('Please run [composer dbup-exec] to execute scripts.', 'yellow');
        }
        
        exit(0);
    }
    
    protected function executeQuery($sqlFile, $dir)
    {
        $dbConf = $this->db->getConfig();
        $cmd = sprintf('mysql --default-character-set=utf8 --host=%s --user=%s --password=%s %s < %s',
                $dbConf['host'], $dbConf['username'], $dbConf['password'], $dbConf['dbname'],
                $dir . '/' . $sqlFile);
        $this->writeLn("Executing [$sqlFile]", 'yellow');
        $retval = null;
        $lastLine = system($cmd, $retval);        
        //update db log
        if($retval == 0){
            try {
                $this->db->insert('upgrade_db_log', array(
                    'file'  => $sqlFile,
                    'insert_dt' => date('c')
                ));
                $this->writeLn("$sqlFile OK", 'green');
            } catch (Exception $exc) {
                $this->writeLn("ERR updating log for [$sqlFile]:\n" . $exc->getMessage(), null, 'red');
            }
        } else {
            $this->writeLn("ERR executing [$sqlFile]:\n" . $lastLine, null, 'red');
        }                  
    }

}