<?php
/**
 * File Helper Class
 *
 * @package HCMS
 * @subpackage File
 * @copyright Horisen
 * @author milan
 */
class HCMS_File_Helper {
    protected $_root = "";
    protected $_webRoot = "";
    protected $_forceRoot = true;
    protected $_options = array();
    protected $_subPath = "";
    /**
     *
     * @var Application_Model_Application
     */
    protected $_application = null;

    protected $_lastErrorMessage = '';

    
    protected $_defaultExtensions = array("pjpeg", "jpeg", "jpg", "png", "x-png", "gif",
                                "html","htm","css","json","eot","svg","ttf","woff",
                                "mp3", "mp4", "aac", "otf","zip","pdf");

    
    protected $_defaultMimeTypes = array( 'image/pjpeg', 'image/jpeg', 'image/jpg', 'image/png', 'image/x-png', 'image/gif',
                                'image/svg+xml','image/svg','application/octet-stream',
                                'text/html','text/plain','text/plain charset=us-ascii','text/plain charset=utf-8',
                                'text/x-c','text/x-c charset=us-ascii','text/css',
                                'audio/mpeg3', 'audio/x-mpeg-3', 'audio/mpeg','application/zip','application/pdf');
    
    /**
     * Constructor
     *
     */
    function __construct($application,$options) {
        $this->_application = $application;
        $this->_options = $options;
        $this->_subPath = $this->_application->get_id();
        $this->_root = $this->_options['root'] . "/" .  $this->_subPath;
        $this->_webRoot = $this->_options['webRoot'] . "/" . $this->_subPath;
        $this->_forceRoot = $this->_options['forceRoot'];
    }

    /**
     * Create directory for defined page and module
     */
    public function createPageDir(){
        if(is_dir($this->_options['root'] . DIRECTORY_SEPARATOR . $this->_subPath)){
            return true;
        }
        $parts = explode("/", $this->_subPath);
        $currPath = $this->_options['root'];
        foreach ($parts as $part) {
            $currPath .= "/" . $part;
            if(!is_dir($currPath)){
                mkdir($currPath);
            }
        }
    }

    /**
     * Get real path
     *
     * @param string $path
     * @return array | false
     */
    public function getPath($path){
        if(substr($path, 0, 1) != '/'){
            $path = '/' . $path;
        }
        $realPath = realpath($this->_root . $path);
        if($this->_forceRoot && strpos($realPath, realpath($this->_root)) !== 0){
            //echo "path: [$path], real path: [$realPath]  root: [$this->_root]" ;
            return false;
        }
        return array(
            "real"  => $realPath,
            "web"   => $this->_webRoot . $path
        );
    }

    /**
     * Converts web path to user path - without root
     * @param string $path
     * @return string|false
     */
    public function getUserPath($path){
        if(strpos($path, $this->_webRoot) !== 0){
            return false;
        }
        return substr($path, strlen($this->_webRoot));
    }

    /**
     * Get Directory size in bytes
     *
     * @param string $dir directory abs path
     * @return int
     */
    public function getDirSize($dir){
        $io = popen('/usr/bin/du -sb ' . $dir, 'r');
        $size = fgets($io,4096);

        $desc = explode("\t", $size);
        if(count($desc) < 2){
            $desc = explode(" ", $size);
        }
        pclose($io);
        $size = (int)$desc[0];
        return $size;
    }

    /**
     * Get root dir
     *
     * @return string
     */
    public function getRoot(){
        return $this->_root;
    }

    /**
     * Get user root dir size in bytes
     *
     * @return int
     */
    public function getRootDirSize(){
        return $this->getDirSize($this->getRoot());
    }

    /**
     * Hosting quota in bytes
     * 
     * @return int
     */
    public function getQuota(){
        //get hosting quota
        $quota = (int)$this->_application->get_settings('hosting_quota');
        if(!isset ($quota) || $quota <= 0){
            //10MB gratis
            $quota = 10 * 1024 * 1024;
        }
        return $quota;
    }

    /**
     * Get free space in page dir based on hosting quota
     * 
     * @return int
     */
    public function getFreeSpace(){
        //get hosting quota
        $quota =$this->getQuota();
        $usedSpace = $this->getRootDirSize();
        $freeSpace = ($quota > $usedSpace)? ($quota - $usedSpace): 0;
        return $freeSpace;
    }

    /**
     * Store uploaded file in proper directory for module/page
     *
     * $params = array(
     *   'field' => 'form field name'
     *   'dir' => 'subdirectory for this file'
     *   'extensions' => array('jpg','png' ...)
     *   'mimetypes' => array('image/jpg', 'image/png')
     * )
     *
     * @param array $params
     * @return string|false
     */
    public function upload(array $params){
        //check dir path
        $dir = $params['dir'];
        $paths = $this->getPath($dir);
        if($paths === false){
            $this->_lastErrorMessage = "Invalid dir [$dir]";
            return false;
        }
        if(!isset ($params['field'])){
            $this->_lastErrorMessage = "Field not specifield";
            return false;            
        }
        $fieldName = $params['field'];
        $freeSpace = $this->getFreeSpace();
        $extensions = array();
        $mimeTypes = array();
        //valid extensions
        if(isset ($params['extensions']) && is_array($params['extensions'])){
            $extensions = $params['extensions'];
        }
        //valid mime types
        if(isset ($params['mimetypes']) && is_array($params['mimetypes'])){
            $mimeTypes = $params['mimetypes'];
        }
        try{
            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->addValidator("Count",false, array("min"=>1, "max"=>5))
                    ->addValidator("Size",false,array("max"=>$freeSpace))
                    ->addValidator("Extension",false,$extensions)
                    ->addValidator('MimeType', false, $mimeTypes);
            $adapter->setDestination($paths['real']);
            $files = $adapter->getFileInfo();

            $result = array();
            
            foreach ($files as $file => $info) {
                // file uploaded ?
                if (!$adapter->isUploaded($info['name']) || !$adapter->isValid($info['name'])) {
                    $this->_lastErrorMessage  = implode(" ", $adapter->getMessages());
                    continue;
                }

                if(strpos($file, $fieldName) !== 0){
                    continue;
                }

                if($adapter->receive($info["name"])){
                    $result[] = $dir."/".$info["name"];
                }
            }
            if(count($result)){
                return $result;
            }
            else{
                if($this->_lastErrorMessage == ''){
                    $this->_lastErrorMessage  = "No files uploaded";
                }
                else{
                    $this->_lastErrorMessage  .= ". No files uploaded";
                }
                
                return false;
            }
        }
        catch (Exception $ex){
            $this->_lastErrorMessage = $ex->getMessage();
            return false;
        }
    }

    /**
     * Get upload error message
     * 
     * @return string
     */
    public function getLastErrorMessage(){
        return $this->_lastErrorMessage;
    }
    public function getDefaultExtensions() {
        return $this->_defaultExtensions;
    }

    public function getDefaultMimeTypes() {
        return $this->_defaultMimeTypes;
    }
}