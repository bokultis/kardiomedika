<?php
/**
 * Base entity model.
 *
 * @package HCMS
 * @subpackage Utils
 * @copyright Horisen
 * @author zeka
 */
class HCMS_Utils_Code {

    /**
     * create code
     *
     * @param int $timestamp
     * @return string
     */
    static public function createCode(){
        //We don't need a 32 character long string so we trim it down to 5
        return md5(rand(0,999));
    }
}
