<?php
/**
 * File Helper Class
 *
 * @package HCMS
 * @subpackage File
 * @copyright Horisen
 * @author boris
 */
class HCMS_File_GenericHelper {
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

    protected $_maxSize = 10485760;
    
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
        $this->_tmpPath = $this->_options['tmpFolder'] . "/" .  $this->_subPath;
        $this->_webRoot = $this->_options['webRoot'] . "/" . $this->_subPath;
        $this->_forceRoot = $this->_options['forceRoot'];
    }
    
    /**
     * Create directory for defined field unique directory
     */
    public function createFieldDir($tmpDir, $tmp = false){
        $rootLocation = $this->_options['root'];
        
        if($tmp)  $rootLocation = $this->_options['tmpFolder'];

        if(is_dir($rootLocation . DIRECTORY_SEPARATOR . $this->_subPath . DIRECTORY_SEPARATOR . $tmpDir)){
            return $rootLocation . DIRECTORY_SEPARATOR . $this->_subPath . DIRECTORY_SEPARATOR . $tmpDir;
        }
        if(!is_dir($rootLocation)){
            mkdir($rootLocation);
        }
        $parts = explode(DIRECTORY_SEPARATOR, $tmpDir);
        $currPath = $rootLocation . DIRECTORY_SEPARATOR . $this->_subPath;
        
        if(!is_dir($currPath)){
            mkdir($currPath);
        }
        
        foreach ($parts as $part) {
            $currPath .= DIRECTORY_SEPARATOR . $part;
            if(!is_dir($currPath)){
                mkdir($currPath);
            }
        }
        
        return $currPath;
    }
 
    
    /**
     * Move uploaded file form tmp folder to public final folder
     */
    public function moveToFinalDest($filePath){
                
        $parts = explode("/", $filePath);
        $parts = array_slice($parts, 0, count($parts)-1);
        $dir = implode("/", $parts);
        
        $tmpPath = $this->_options['tmpFolder'] . DIRECTORY_SEPARATOR . $this->_subPath . DIRECTORY_SEPARATOR . $filePath;
        $finalPath = $this->_options['root'] . DIRECTORY_SEPARATOR . $this->_subPath . DIRECTORY_SEPARATOR . $filePath;
        
        if(file_exists($tmpPath)){
            $this->createFieldDir($dir);
            return rename($tmpPath, $finalPath);
        }   
        return false;
    }
    
    /**
     * Get tmp path
     *
     * @param string $path
     * @return array | false
     */
    public function getTmpPath($path){
        if(substr($path, 0, 1) != '/'){
            $path = '/' . $path;
        }

        $realPath = realpath($this->_tmpPath . $path);
        if(strpos($realPath, realpath($this->_tmpPath)) !== 0){
            //echo "path: [$path], real path: [$realPath]  root: [$this->_root]" ;
            return false;
        }
        return array(
            "real"  => $realPath,
        );
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
        $paths = $this->getTmpPath($dir);
        if($paths === false){
            $this->_lastErrorMessage = "Invalid dir [$dir]";
            return false;
        }
        if(!isset ($params['field'])){
            $this->_lastErrorMessage = "Field not specifield";
            return false;            
        }
        $fieldName = $params['field'];
        $extensions = array();
        $maxSize = '';
        $mimeTypes = array();
        //valid extensions
        if(isset ($params['extensions']) && is_array($params['extensions'])){
            $extensions = $params['extensions'];
        }else{
            $extensions = $this->_defaultExtensions;
        }
        
        //max file size
        if(isset ($params['maxsize']) && $params['maxsize'] != ''){
            $maxSize = $params['maxsize'];
        }else{
            $maxSize = $this->_maxSize;
        }
        //valid mime types
        if(isset ($params['mimetypes']) && is_array($params['mimetypes'])){
            $mimeTypes = $params['mimetypes'];
        }else{
            $mimeTypes = $this->_defaultMimeTypes;
        }
        try{
            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->addValidator("Count",false, array("min"=>1, "max"=>5))
                    ->addValidator("Size",false,array("max"=>$maxSize))
                    ->addValidator("Extension",false,$extensions)
                    ->addValidator('MimeType', false, $mimeTypes);
            $adapter->setDestination($paths['real']);
            $files = $adapter->getFileInfo();
            
            $result = array();
            
            foreach ($files as $file => $info) {
                // file uploaded ?
                if (!$adapter->isUploaded($info['name']) || !$adapter->isValid($info['name'])) {
                    //$this->_lastErrorMessage  = implode(" ", $adapter->getMessages());
                    $this->_lastErrorMessage  = "No files uploaded";
                    continue;
                }

                if(strpos($file, $fieldName) !== 0){
                    continue;
                }

                if($adapter->receive($info["name"])){
                    $result['name'] = $info["name"];
                    $result['path'] = $dir."/".$info["name"];
                    $result['size'] = $info["size"];
                    $result['type'] = $info["type"];
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
                    $this->_lastErrorMessage  = "No files uploaded";
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
    
    public function pmcCleaner(){
        $tmpPath = $this->_options['tmpFolder'] . DIRECTORY_SEPARATOR . $this->_subPath;
        $dir = new RecursiveDirectoryIterator($tmpPath);
        foreach (new RecursiveIteratorIterator($dir) as $filename => $file) {
            if(is_file($file) && (time() - filemtime($filename) > $this->_options['tmpFileLifetime'])){
                unlink($filename);
            }
        }
    }
}