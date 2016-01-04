<?php
/**
 * Base entity model.
 *
 * @package HCMS
 * @subpackage Utils
 * @copyright Horisen
 * @author marko
 */
class HCMS_Utils_Date {
    /**
     * Convert Date from Iso to Local
     * @param string $dateStr
     * @param string $additional_format
     * @return string 
     */
    public static function dateIsoToLocal($dateStr, $additional_format = ""){

        $locale = Zend_Registry::get('Zend_Locale');
        if( !($locale instanceof Zend_Locale) ) {
            require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception("Cannot resolve Zend Locale format by default, no application wide locale is set.");
        }
        if($dateStr){
            //Zend_Locale_Format
            $format = Zend_Locale_Format::getDateFormat($locale);
            if($additional_format != "") $format = $format . " " .  $additional_format;
            $date = new Zend_Date($dateStr, Zend_Date::ISO_8601);
            return $date->toString($format);
        }else
        return ;
    }
    /**
     *
     * @param type $dateStr
     * @return type 
     * 
     */
    public static function dateLocalToIso($dateStr){

        $date = new Zend_Date($dateStr);

        return $date->getIso();
    }
    /**
     *
     * @param type $dateStr
     * @param type $format
     * @return type 
     */
    public static function dateLocalToCustom($dateStr, $format=''){
        $date = new Zend_Date($dateStr);
        if($format != ''){
            return $date->toString($format);
        }
        else    return $date->getIso();
    }
    /**
     * This function only resolves the default format from Zend Locale to
     * a date picker readable format.
     *
     * @param string $format Date format. If not set, it will used format date defined in Zend Local.
     * @return string format for date picker.
     */
    public static function resolveZendLocaleToDatePickerFormat($format=null)
    {
        /*
         * key is Zend Locale format value is date picker format.
         */
        $dateFormat = array(
            'EEEEE' => 'D', 'EEEE' => 'DD', 'EEE' => 'D', 'EE' => 'D', 'E' => 'D', 'dd' => 'd',
            'MMMM' => 'MM', 'MMM' => 'M', 'MM' => 'm', 'M' => 'm','mm' => 'm',
            'YYYYY' => 'yy', 'YYYY' => 'yy', 'YYY' => 'yy', 'YY' => 'y', 'Y' => 'y',
            'yyyyy' => 'yy',
            'yyy' => 'yy', 'yy' => 'y',
            //added by Nikola
            'y' => 'yy',//to four digits for year.
            //
            'G' => '', 'e' => '', 'a' => '', 'h' => '', 'H' => '', 'm' => '',
            's' => '', 'S' => '', 'z' => '', 'Z' => '', 'A' => '',
        );

        return self::resolveZendLocaleToDateFormat($dateFormat, $format);
    }

    /**
     * This function only resolves the default format from Zend Locale to appropriate format
     * defined via $dateFormat array mapper.
     *
     * @param array $dateFormat array for mapping Zend Local to new format.
     * @param string|null $format Date format. If not set, it will used format date defined in Zend Local.
     * @return new format
     */
    public static function resolveZendLocaleToDateFormat(array $dateFormat, $format=null)
    {
        if($format == null) {
            $locale = Zend_Registry::get('Zend_Locale');
            if( !($locale instanceof Zend_Locale) ) {
                require_once 'Zend/Locale/Exception.php';
                throw new Zend_Locale_Exception("Cannot resolve Zend Locale format by default, no application wide locale is set.");
            }
            /**
             * @see Zend_Locale_Format
             */
            require_once "Zend/Locale/Format.php";
            $format = Zend_Locale_Format::getDateFormat($locale);
        }

        $newFormat = "";
        $isText = false;
        $i = 0;
        while($i < strlen($format)) {
            $chr = $format[$i];
            if($chr == '"' || $chr == "'") {
                $isText = !$isText;
            }
            $replaced = false;
            if($isText == false) {
                foreach($dateFormat as $zl => $jql) {
                    if(substr($format, $i, strlen($zl)) == $zl) {
                        $chr = $jql;
                        $i += strlen($zl);
                        $replaced = true;
                    }
                }
            }
            if($replaced == false) {
                $i++;
            }
            $newFormat .= $chr;
        }

        return $newFormat;
    }
}

