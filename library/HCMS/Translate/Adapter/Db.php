<?php
/**
 * Customized Zend_Translate_Adapter for DB
 * Loads translations from DB
 *
 * @package HCMS
 * @subpackage Translate
 * @copyright Horisen
 * @author milan
 */



class HCMS_Translate_Adapter_Db extends Zend_Translate_Adapter
{
    /**
     * Where to store untraslated keys
     * @var string
     */
    private $_logSection = 'global';

    /**
     * From Where to read translations
     * @var string
     */
    private static $_section = 'global';

    /**
     * Translates the given string
     * returns the translation
     *
     * @see Zend_Locale
     * @param  string|array       $messageId Translation string, or Array for plural translations
     * @param  string|Zend_Locale $locale    (optional) Locale/Language to use, identical with
     *                                       locale identifier, @see Zend_Locale for more information
     * @param  string $section
     * @return string
     */
    public function translate($messageId, $locale = null, $section = 'global')
    {
        $this->_logSection = $section;
        $translated = parent::translate($messageId, $locale);
        if ($translated == '') {
            return $messageId;
        }
        return $translated;
    }

    /**
     * Load translation data
     *
     * @param  string|array  $data
     * @param  string        $locale  Locale/Language to add data for, identical with locale identifier,
     *                                see Zend_Locale for more information
     * @param  array         $options OPTIONAL Options to use
     * @return array
     */
    protected function _loadTranslationData($data, $locale, array $options = array())
    {
        if(isset ($options['section'])){
            self::$_section = $options['section'];
        }

        /* @var $translateCache Application_Model_TranslateMapper */
        $translateCache = HCMS_Cache::getInstance()->getObjectCache(Application_Model_TranslateMapper::getInstance());
        //read data from cache object
        $data = $translateCache->__call("load", array($locale, self::$_section));
        $options = array_merge($this->_options, $options);

        if((isset ($this->_options['clear']) &&  $this->_options['clear'] === true) || !isset ($this->_translate[$locale])){
            $this->_translate[$locale] = array();
        }

        $this->_translate[$locale] = array_merge($this->_translate[$locale], $data);
    }


    /**
     * returns the adapters name
     *
     * @return string
     */
    public function toString()
    {
        return "Db";
    }

    /**
     * Is the wished language available ?
     *
     * @see    Zend_Locale
     * @param  string|Zend_Locale $locale Language to search for, identical with locale identifier,
     *                                    @see Zend_Locale for more information
     *
     * @param  boolean $frontEnd
     * @return boolean
     */
    public function isAvailable($locale, $frontEnd = false)
    {
        //print_r($this->_options);
        return Application_Model_TranslateMapper::getInstance()->isLangAvailable($locale, $frontEnd);
    }

    /**
     * Is the wished language available ?
     *
     * @see    Zend_Locale
     * @param  string|Zend_Locale $locale Language to search for, identical with locale identifier,
     *                                    @see Zend_Locale for more information
     * @param  boolean $frontEnd
     * @return boolean
     */
    public static function isLangAvailable($locale, $frontEnd = false)
    {
        return Application_Model_TranslateMapper::getInstance()->isLangAvailable($locale, $frontEnd);
    }

    /**
     * Logs a message when the log option is set
     *
     * @param string $message Message to log
     * @param String $locale  Locale to log
     */
    protected function _log($message, $locale) {
        if ($this->_options['logUntranslated']) {
            $transString = $message;
            $message = str_replace('%message%', $message, $this->_options['logMessage']);
            $message = str_replace('%locale%', $locale, $message);
            $logger = Zend_Registry::get('Zend_Log');
            $logger->log($message, Zend_Log::NOTICE);

            //save to DB
            $value = $transString;

            $defaultLang = HCMS_Utils::getDefaultLocale();
            //add indication prefix for non default
            if($locale != $defaultLang){
                $value = '';
            }
            try {
                Application_Model_TranslateMapper::getInstance()->addTranslation(array(
                    'key'           => $transString,
                    'value'         => $value,
                    'section'       => $this->_logSection
                ), $locale);
            }
            catch (Exception $exc) {
                $logger->log("Translation DB: " . $exc,Zend_Log::NOTICE);
            }
        }
    }


    /**
     * Activate Zend translate DB and language, and locale
     *
     * @param string $language
     * @return string
     */
    public static function activate($language, $section = 'global'){
        //default FB locale, ex: en_US
        $language = Application_Model_TranslateMapper::getInstance()->getLang($language);
        //echo "Locale is chosen: " . $language . "<br>";

        //set locale
        Zend_Registry::get('Zend_Locale')->setLocale($language);

        /* @var $translate Zend_Translate */
        if(!Zend_Registry::isRegistered('Zend_Translate')){
            $translate = new Zend_Translate(
                array(
                    'adapter'           => 'HCMS_Translate_Adapter_Db',
                    'content'           => 'db',
                    'locale'            => $language,
                    'logMessage'        => "Missing '%message%' within locale '%locale%'",
                    'logPriority'       => Zend_Log::ALERT,
                    'logUntranslated'   => true,
                    'disableNotices'    => true,
                    'section'           => $section
                )
            );
            Zend_Registry::set('Zend_Translate', $translate);
        }
        else{
            $translate = Zend_Registry::get('Zend_Translate');
        }
        $translate->setLocale($language);

        return $language;
    }

}