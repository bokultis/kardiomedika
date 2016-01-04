<?php

/**
 * Misc utility functions
 *
 * @package HCMS
 * @subpackage Utils
 * @copyright Horisen
 * @author milan
 */
class HCMS_Utils {

    /**
     * Get real client IP address
     *
     * @return string
     */
    public static function getRealIpAddr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Get human readable filesize
     *
     * @param int $a_bytes
     * @return string
     */
    public static function formatBytes($a_bytes) {
        if ($a_bytes < 1024) {
            return $a_bytes . ' B';
        } elseif ($a_bytes < 1048576) {
            return round($a_bytes / 1024, 2) . ' KB';
        } elseif ($a_bytes < 1073741824) {
            return round($a_bytes / 1048576, 2) . ' MB';
        } elseif ($a_bytes < 1099511627776) {
            return round($a_bytes / 1073741824, 2) . ' GB';
        } elseif ($a_bytes < 1125899906842624) {
            return round($a_bytes / 1099511627776, 2) . ' TB';
        } elseif ($a_bytes < 1152921504606846976) {
            return round($a_bytes / 1125899906842624, 2) . ' PB';
        } elseif ($a_bytes < 1180591620717411303424) {
            return round($a_bytes / 1152921504606846976, 2) . ' EB';
        } elseif ($a_bytes < 1208925819614629174706176) {
            return round($a_bytes / 1180591620717411303424, 2) . ' ZB';
        } else {
            return round($a_bytes / 1208925819614629174706176, 2) . ' YB';
        }
    }

    /**
     * Get default locale/lang
     *
     * @return string|null
     */
    public static function getDefaultLocale() {
        return Application_Model_TranslateMapper::getInstance()->getDefaultLang();
        /* $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
          $options = $bootstrap->getOption('resources');
          if(isset ($options) && isset ($options['locale']) && isset ($options['locale']['default'])) {
          return $options['locale']['default'];
          }
          else {
          return null;
          } */
    }

    public static function base32Encode($inString) {
        $outString = "";
        $compBits = "";
        $BASE32_TABLE = array(
            '00000' => 0x61,
            '00001' => 0x62,
            '00010' => 0x63,
            '00011' => 0x64,
            '00100' => 0x65,
            '00101' => 0x66,
            '00110' => 0x67,
            '00111' => 0x68,
            '01000' => 0x69,
            '01001' => 0x6a,
            '01010' => 0x6b,
            '01011' => 0x6c,
            '01100' => 0x6d,
            '01101' => 0x6e,
            '01110' => 0x6f,
            '01111' => 0x70,
            '10000' => 0x71,
            '10001' => 0x72,
            '10010' => 0x73,
            '10011' => 0x74,
            '10100' => 0x75,
            '10101' => 0x76,
            '10110' => 0x77,
            '10111' => 0x78,
            '11000' => 0x79,
            '11001' => 0x7a,
            '11010' => 0x32,
            '11011' => 0x33,
            '11100' => 0x34,
            '11101' => 0x35,
            '11110' => 0x36,
            '11111' => 0x37,
        );

        /* Turn the compressed string into a string that represents the bits as 0 and 1. */
        for ($i = 0; $i < strlen($inString); $i++) {
            $compBits .= str_pad(decbin(ord(substr($inString, $i, 1))), 8, '0', STR_PAD_LEFT);
        }

        /* Pad the value with enough 0's to make it a multiple of 5 */
        if ((strlen($compBits) % 5) != 0) {
            $compBits = str_pad($compBits, strlen($compBits) + (5 - (strlen($compBits) % 5)), '0', STR_PAD_RIGHT);
        }

        /* Create an array by chunking it every 5 chars */
        $fiveBitsArray = explode("\n", rtrim(chunk_split($compBits, 5, "\n")));

        /* Look-up each chunk and add it to $outstring */
        foreach ($fiveBitsArray as $fiveBitsString) {
            $outString .= chr($BASE32_TABLE[$fiveBitsString]);
        }

        return $outString;
    }

    /**
     * Function will generate json format and option format strings based on one-level array.
     * This strings are generated in way appropriate for jgrid select option fields.
     *
     * @param array $optionsArr One level array.
     * @param string $jsonEncodedOptionsFormat Json encoded array.
     * @param string $optionsFormat Option string.
     * @param boolean $translate Set to true if options values should be translated.
     * @example Here is one example:
     *
     * $optionsArr(<br/>
     *      [A] => Active,<br/>
     *      [D] => Inactive
     * )
     * <br/><br/>
     * $jsonEncodedOptionsFormat:
     * {"A":"Active","D":"Inactive"}
     * <br/><br/>
     * $optionsFormat:
     * A:Active; D:Inactive
     *
     * 
     */
    public static function generateFormatedOptions(array $optionsArr, &$jsonEncodedOptionsFormat, &$optionsFormat, $translate = true) {
        $jsonEncodedOptionsFormat = "";
        $optionsFormat = "";
        if (!is_array($optionsArr) || count($optionsArr) == 0) {
            return;
        }
        if ($translate) {
            $tr = Zend_Registry::get('Zend_Translate');
            if (!($tr instanceof Zend_Translate)) {
                throw new Zend_Translate_Exception("Zend Translate is not available.");
            }
        }
        $status = array();
        $statusFormater = array();
        foreach ($optionsArr as $flag => $value) {
            $trValue = ($translate) ? $tr->_(addslashes($value)) : addslashes($value);
            $status[addslashes($flag)] = addslashes($flag) . ":" . $trValue;
            $statusFormater[addslashes($flag)] = $trValue;
        }
        $jsonEncodedOptionsFormat = json_encode($statusFormater);
        $optionsFormat = join(";", $status);
    }

    public static function mergeLocalConfig($filePath)
    {
        $result = require $filePath;
        $localFilePath = dirname($filePath) . DIRECTORY_SEPARATOR . 'local.' . basename($filePath);
        if (file_exists($localFilePath)) {
            $result = array_merge($result, require $localFilePath);
        }
        return $result;
    }

    /**
     * Load array config from theme, customization, module or app
     * 
     * @param string $fileName
     * @param string $module
     * @return array|null
     */
    public static function loadThemeConfig($fileName, $module = null) {
        $mvc = Zend_Layout::getMvcInstance();
        if ($mvc) {
            $mvcView = clone $mvc->getView();
        }
        //try theme's config
        if (isset($mvc) && isset($mvcView->theme)) {
            $filePath = APPLICATION_PATH . '/../themes/' . $mvcView->theme . '/configs/';
            if ($module) {
                $filePath .= $module . '/';
            }
            $filePath .= $fileName;
            if (file_exists($filePath)) {
                return self::mergeLocalConfig($filePath);
            }
        }
        //module's config        
        if ($module) {
            //try customization first
            $filePath = APPLICATION_PATH . '/../customization/modules/' . $module . '/configs/' . $fileName;
            if (!file_exists($filePath)) {
                $filePath = APPLICATION_PATH . '/modules/' . $module . '/configs/' . $fileName;
            }
        }
        //global config
        else {
            $filePath = APPLICATION_PATH . '/configs/' . $fileName;
        }
        $result = null;
        if (file_exists($filePath)) {
            $result = self::mergeLocalConfig($filePath);
        }
        
        return $result;
    }
}