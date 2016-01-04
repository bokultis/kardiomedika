<?php
/**
 * Translate Mapper
 *
 * @package Application
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Application_Model_TranslateMapper extends HCMS_Model_Mapper {
    /**
     * singleton instance
     *
     * @var Application_Model_TranslateMapper
     */
    protected static $_instance = null;

    private static $_languages = array();

    private $_langsLoaded = false;
    private $_defaultLang = null;
    
    /**
     *
     * @var Application_Model_DbTable_Page
     */
    protected $_dbTable;

    /**
     * private constructor
     */
    private function  __construct()
    {
        $this->_dbTable = new Application_Model_DbTable_Translate();
        $this->_initLogger();
    }

    /**
     * get instance
     *
     *
     * @return Application_Model_TranslateMapper
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Load Translations
     *
     * @param string $locale
     * @param string $section
     * @param boolean $excludeGlobalSection
     * @return array
     */
    public function load($locale,$section = null,$excludeGlobalSection = false) {
        $this->_log("Loading translations from DB with params locale: $locale,section: $section,excludeGlobalSection: $excludeGlobalSection", Zend_Log::INFO);
        
        $locale = $this->getLang($locale);
        $select = $this->_dbTable->select();

        $select ->from(array('t' => 'translate'),array('t.key','t.value'))
                ->joinLeft(array('l' => 'translate_language'), 'l.id = t.language_id',array())
                ->where('l.code = ?', $locale);

        if(isset ($section) && $section != 'global'){
            if($excludeGlobalSection){
                $select->where("t.section = ?", $section);
            }
            else{
                $select->where("t.section = 'global' OR t.section = ?", $section);
            }
        }
        else{
            $select->where("t.section = 'global'");
        }

        //echo $select->__toString();
        $resultSet = $this->_dbTable->fetchAll($select);
        $trScheme = array();
        if (0 == count($resultSet)) {
            return $trScheme;
        }
        foreach ($resultSet as $trElement){
            $trScheme[$trElement['key']] = $trElement['value'];
        }

        return $trScheme;
    }

    /**
     * Add new translation key
     * 
     * @param array $translation
     * @param string $locale
     * @return int|false
     */
    public function addTranslation($translation,$locale){
        
        if(!$this->isLangAvailable($locale)){
            return false;
        }
        if(!isset ($translation['language_id'])){
            $translation['language_id'] = self::$_languages[$locale]['id'];
        }
        //clean cache
        HCMS_Cache::getInstance()->getObjectCache(Application_Model_TranslateMapper::getInstance())->clean();
        return $this->_dbTable->insert($translation);
    }

    /**
     * Load langs from db
     * 
     * @return mixed
     */
    private function _loadLangs(){
        if($this->_langsLoaded){
            return;
        }
        /* @var $select Zend_Db_Select */
        $select = $this->_dbTable->getAdapter()->select();
        $select ->from(array('l' => 'translate_language'),array('l.code','l.id','l.name','l.default','l.front_enabled'));
        $stmt = $select->query();
        $langs = $stmt->fetchAll();
        foreach($langs as $lang){
            self::$_languages[$lang['code']] = array(
                'id'            => $lang['id'],
                'code'          => $lang['code'],
                'name'          => $lang['name'],
                'default'       => $lang['default'] == 'yes',
                'front_enabled' => $lang['front_enabled'] == 'yes'
            );
            //if default lang is not defined use first one as default
            if($lang['default'] == 'yes' || !isset ($this->_defaultLang)){
                $this->_defaultLang = $lang['code'];
            }
        }

        $this->_langsLoaded = true;        
    }

    /**
     * Is the wished language available ?
     *
     * @see    Zend_Locale
     * @param  string|Zend_Locale $locale Language to search for, identical with locale identifier,
     *                                    @see Zend_Locale for more information
     * @param  boolean $frontOnly check if should be available on front end
     * @return boolean
     */
    public function isLangAvailable($locale, $frontEnd = false)
    {
        $this->_loadLangs();
        //print_r($this->_options);
        $result = isset (self::$_languages[(string) $locale]);
        //if not found no need to go further - it's false
        if(!$result){
            return false;
        }
        //if no need to check front return true
        if(!$frontEnd){
            return true;
        }
        //check if land is enabled on front
        else{
            return self::$_languages[(string) $locale]['front_enabled'];
        }
    }

    /**
     * Get languages array
     *
     * @return array
     */
    public function getLanguages(){
        return self::$_languages;
    }

    /**
     * Get lang
     *
     * @param string $language
     * @return string
     */
    public function getLang($language){
        //default FB locale, ex: en_US
        if(!$this->isLangAvailable($language)){
            //try sup locale
            $langParts = explode('_', $language);
            if(isset ($langParts[0])){
                $language = $langParts[0];
            }
        }
        if(!$this->isLangAvailable($language)){
            $language = $this->getDefaultLang();
        }
        if(!$this->isLangAvailable($language)){
            $language = 'en';
        }

        return $language;
    }

    /**
     * Get lang defined as default
     * 
     * @return string
     */
    public function getDefaultLang(){
        $this->_loadLangs();
        if(isset ($this->_defaultLang)){
            return $this->_defaultLang;
        }
        else{
            return (string)Zend_Registry::get('Zend_Locale')->getLanguage();
        }
    }
}
