<?php
/**
 * Execution profiler
 *
 * @package HCMS
 * @subpackage Utils
 * @copyright Horisen
 * @author milan
 */
class HCMS_Utils_Profiler {

    protected static $_timestamp = 0;
    
    /**
     * Add checkpoint
     *
     * @param int $timestamp
     * @return string
     */
    static public function checkpoint($name) {
        if(APPLICATION_ENV != 'development'){
            return;
        }

        $currTime = microtime(true);
        if(self::$_timestamp == 0){
            $execTime = 0;
        }
        else{
            $execTime = $currTime - self::$_timestamp;
        }
        self::$_timestamp = $currTime;
        if($execTime > 0.01){
            $exl = "!!!";
        }
        else{
            $exl = "";
        }
        $msg = "Profiler[$name]$exl: exec time: " . number_format($execTime, 3) ." s";

        if(class_exists('Zend_Registry') && Zend_Registry::isRegistered('Zend_Log')){
            Zend_Registry::get('Zend_Log')->log($msg, Zend_Log::ALERT);
        }
        else{
            error_log($msg);
        }
    }

}
