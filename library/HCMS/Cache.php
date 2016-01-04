<?php
/**
 * Cache singleton class
 *
 * @package HCMS
 * @subpackage Cache
 * @copyright Horisen
 * @author milan
 */
class HCMS_Cache {

    /**
     * singleton instance
     *
     * @var HCMS_Cache
     */
    protected static $_instance = null;

    /**
     *
     * @var Zend_Cache_Manager
     */
    protected $_cacheManager = null;

    /**
     * private constructor
     */
    private function  __construct()
    {
        if(Zend_Registry::isRegistered('cachemanager')){
            $this->_cacheManager = Zend_Registry::get('cachemanager');
        }
        else{
            throw new Zend_Exception("Cachemanager not defined");
        }
        
    }

    /**
     * get instance
     *
     *
     * @return HCMS_Cache
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
     * Get page cache object
     *
     * @return Zend_Cache_Frontend_Page
     */
    protected function _getPageCache(){
        /* @var $cache Zend_Cache_Frontend_Page */
        return $this->_cacheManager->getCache('page');
    }

    /**
     * Get tags array
     * 
     * @param array $options
     * @return array
     */
    protected function _getPageCacheTags(array $options){
        $tags = array();
        if(isset ($options['page_id'])){
            $tags[] = "page_id-" . $options['page_id'];
        }
        if(isset ($options['admin'])){
            $tags[] = "admin-" . $options['admin'];
        }
        if(isset ($options['liked'])){
            $tags[] = "liked-" . $options['liked'];
        }
        if(isset ($options['custom_id'])){
            $tags[] = "custom_id-" . $options['custom_id'];
        }
        return $tags;
    }

    /**
     * Get page cache id
     *
     * @param array $options
     * @return string
     */
    protected function _getPageCacheId(array $options){
        return implode("_", array_values($this->_getPageCacheTags($options)));
    }

    /**
     * Run page from cache
     *
     * @param array $options
     */
    public function startPageCache(array $options){
        $cache = $this->_getPageCache();
        $defOptions = $cache->getOption('default_options');
        //tag cache
        $defOptions['tags'] = $this->_getPageCacheTags($options);
        $cache->setOption('default_options', $defOptions);
        $cache->start($this->_getCacheId($options));
    }

    /**
     * Clean all cached paged tagged with facebook id
     * 
     * @param array $options
     */
    public function cleanPageCache($options){
        $cache = $this->_getPageCache();
        $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,$this->_getPageCacheTags($options));
    }

    /**
     * Get cached object
     *
     * @param stdClass|string $object
     *
     * @return Zend_Cache_Frontend_Class
     */
    public function getObjectCache($object){
        /* @var $cache Zend_Cache_Frontend_Class */
        $cache = $this->_cacheManager->getCache('class');
        $cache->setOption('cached_entity', $object);
        return $cache;
    }

    /**
     * Get Core cache
     *
     * @return Zend_Cache_Core
     */
    public function getCoreCache(){
        return $this->_cacheManager->getCache('core');
    }

    /**
     * get customized cache object
     * 
     * @param string $templateName
     * @param array $customOptions
     * @return Zend_Cache_Core
     */
    public function getCustomCache($templateName,array $customOptions){
        $this->_cacheManager->setTemplateOptions($templateName, $customOptions);
        return $this->_cacheManager->getCache($templateName);
    }


}
?>
