<?php
/**
 * Util CLI
 *
 * @package HCLI
 * @subpackage Controller
 * @author zeka
 *
 */
class HCLI_Controller_Util {

    /**
     * Encode
     * @param string $name
     * @return string
     */
    public static function encode($name){
        $nameExplode = explode("-",$name);
        if(count($nameExplode) == 0){
            return $name;
        }
        return implode("_q9_", $nameExplode);
    }

    /**
     * Decode string
     * @param string $name
     * @return string
     */
    public static function decode($name){
      $nameExplode = explode("_q9_",$name);
        if(count($nameExplode) == 0){
            return $name;
        }
        return implode("-", $nameExplode);
    }
    
}
?>
