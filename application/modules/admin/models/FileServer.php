<?php

/**
 * File Server Engine Class
 *
 * @package Application
 * @subpackage Models
 * @copyright Horisen
 * @author milan
 */
class Admin_Model_FileServer {
    /**
     *
     * @var HCMS_File_Helper
     */
    protected $_helper = null;

    /**
     * Constructor
     *
     */
    function __construct() {
        $this->_helper = Zend_Registry::get("fileserver_helper");
        //check and create if page dir doesn't exist
        $this->_helper->createPageDir();
    }

    /**
     * get real path
     *
     * @param string $path
     * @return string
     */
    public function getRealPath($path){
        $paths = $this->_helper->getPath($path);
        if($paths === false){
            $this->returnError("Invalid Path [$path]");
        }
        return $paths['real'];
    }
    /**
     * Raise Error
     *
     * @param string $msg
     */
    private function returnError($msg){
        throw new Zend_Exception($msg);
    }

    /**
     * List files/folders in a directory
     *
     * @param string $directory
     * @return string
     */
    public function listing($directory) {
        $dir = $this->getRealPath($directory);
        $result = opendir($dir);
        if ($result === FALSE) {
            $this->returnError('Error listing ' . $directory);
            return false;
        }
        clearstatcache();
        $list = array();

        while (false !== ($file = readdir($result))) {
            //ignore special dirs, and hidden files
            if($file == '.' || $file == '..' || substr($file, 0, 1) == '.'){
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $s = stat($path);
                $list[$file] = array(
                    'type' => 'file',
                    'size' => $s[7],
                    'modified' => $s[9],
                    'path'  => $file
                );
            } elseif (is_dir($path)) {
                $list[$file] = array('type' => 'dir','path'  => $file);
            }
        }
        closedir($result);

        uasort($list, array("Admin_Model_FileServer", "sortFiles"));
        //sort($list);

        return $list;
    }

    private static function sortFiles($a, $b){
        if($a['type'] == "dir" && $b['type'] != "dir"){
            return -1;
        }
        elseif ($a['type'] != "dir" && $b['type'] == "dir") {
            return 1;
        }
        else{
            $al = strtolower($a['path']);
            $bl = strtolower($b['path']);
            if ($al == $bl) {
                return 0;
            }
            return ($al > $bl) ? +1 : -1;
        }
    }

    /**
     * Echo test
     *
     * @param string $test
     * @return string
     */
    public function test($test) {
        return "Hello $test";
    }

    /**
     * Chmod
     *
     * @param int $mode
     * @param string $filename
     * @return boolean
     */
    public function chmod(int $mode, $filename) {
        if (chmod($filename, $mode) === FALSE) {
            $this->returnError('Error changing mode for ' . $filename);
            return false;
        }
        return true;
    }

    /**
     * Delete file
     *
     * @param string $path
     * @return boolean
     */
    public function deleteFile($path) {
        $path = $this->getRealPath($path);
        if (!unlink($path)) {
            $this->returnError('Error deleting ' . $path);
            return false;
        }
        return true;
    }

    /**
     * Check if directory is empty
     * 
     * @param string $dir
     * @return boolean|null
     */
    protected function _isDirEmpty($dir) {
        if (!is_readable($dir)){
            return NULL;
        }
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Delete dir
     *
     * @param string $directory
     * @return boolean
     */
    public function rmdir($directory) {
        $directory = $this->getRealPath($directory);
        if (!$this->_isDirEmpty($directory)) {
            $this->returnError('Cannot delete not empty directory');
            return false;
        }
        if (!rmdir($directory)) {
            $this->returnError('Error deleting ' . $directory);
            return false;
        }
        return true;
    }

    /**
     * Make dir
     *
     * @param string $directory
     * @return boolean
     */
    public function mkdir($dirPath, $dirName) {        
        $dirPath = $this->getRealPath($dirPath);        
        $fullDirPath = $dirPath . "/" . $dirName;
        
        if(is_dir($fullDirPath)){
            $this->returnError(sprintf('Directory [%s] already exists',$dirName));            
            return false;
        }
        if(is_file($fullDirPath)){
            $this->returnError(sprintf('File with the same name [%s] already exists',$dirName));
            return false;
        }
        if (mkdir($fullDirPath) === FALSE) {
            $this->returnError(sprintf('Error creating directory [%s]',$dirName));
            return false;
        }
        return true;
    }

    /**
     * return all recursive files and directories
     *
     * @param string $directory
     * @return array
     */
    public function rlisting($directory) {
        $list = array();
        if ($this->_rlisting($directory, $list) === FALSE) {
            return false;
        } else {
            return $list;
        }
    }

    /**
     *
     * @param string $directory
     * @param array $list
     * @return boolean
     */
    private function _rlisting($directory, &$list) {

        $result = opendir($directory);
        if ($result === FALSE) {
            $this->returnError('Error listing ' . $directory);
            return false;
        }
        clearstatcache();

        while (false !== ($file = readdir($result))) {
            if ($file != "." && $file != "..") {
                $path = $directory . DIRECTORY_SEPARATOR . $file;
                if (is_file($path)) {
                    $s = stat($path);
                    $list[$path] = array(
                        'type' => 'file',
                        'size' => $s[7],
                        'modified' => $s[9]
                    );
                } elseif (is_dir($path)) {
                    $sublist = array();
                    if ($this->_rlisting($path, $sublist) === TRUE) {
                        $list[$path] = array('type' => 'dir', 'sublist' => $sublist);
                    } else {
                        return false;
                    }
                }
            }
        }
        closedir($result);
        //sort($list);
        return true;
    }

    /**
     * Rename
     *
     * @param string $oldname
     * @param string $newname
     * @return boolean
     */
    public function rename($oldname, $newname) {
        if(strpos($newname, '/') !== FALSE){
            $this->returnError('Invalid file name');
            return false;
        }
        $oldname = $this->getRealPath($oldname);
        if(is_file($oldname)) {
            $pathinfo = pathinfo($oldname);
            $newname = $pathinfo['dirname']."/".$newname.".".$pathinfo['extension'];
        }elseif(is_dir($oldname)) {
            $newname =dirname($oldname)."/". $newname;
        }
        if (rename($oldname, $newname) === FALSE) {
            $this->returnError('Error renaming ' . $oldname . ' to ' . $newname);
            return false;
        }
        return true;
    }

    /**
     * Copy
     *
     * @param string $remote_file
     * @param string $local_file
     * @param int $mode
     * @return boolean
     */
    public function copy($remote_file, $local_file, $mode) {
        if (!is_file($local_file)) {
            $this->returnError('File ' . $local_file . ' does not exist!');
            return false;
        }
        if (copy($local_file, $remote_file) === FALSE) {
            $this->returnError('Error copying file ' . $local_file . ' to ' . $remote_file);
            return false;
        }
        return true;
    }

    /**
     * Get Hosting Statistics
     * 
     * @return array
     */
    public function stats(){
        return array(
            'quota' => $this->_helper->getQuota(),
            'used'  => $this->_helper->getRootDirSize(),
            'free'  => $this->_helper->getFreeSpace()
        );
    }
      
    /**
     * Recursivly remove directory
     * 
     * @param string $directory
     * @param boolean $empty
     * @return boolean
     */
    protected function _rrmdir($directory, $empty=FALSE) {
        // if the path has a slash at the end we remove it here
        if(substr($directory,-1) == '/') {
            $directory = substr($directory,0,-1);
        }
        // if the path is not valid or is not a directory ...
        if(!file_exists($directory) || !is_dir($directory)) {
            // ... we return false and exit the function
            return FALSE;
            // ... if the path is not readable
        }elseif(!is_readable($directory)) {
            // ... we return false and exit the function
            return FALSE;
            // ... else if the path is readable
        }else {
            // we open the directory
            $handle = opendir($directory);
            // and scan through the items inside
            while (FALSE !== ($item = readdir($handle))) {
                // if the filepointer is not the current directory
                // or the parent directory
                if($item != '.' && $item != '..') {
                    // we build the new path to delete
                    $path = $directory.'/'.$item;

                    // if the new path is a directory
                    if(is_dir($path)) {
                        // we call this function with the new path
                        $this->_rrmdir($path);
                        // if the new path is a file
                    }else {
                        // we remove the file
                        unlink($path);
                    }
                }
            }
            // close the directory
            closedir($handle);
            // if the option to empty is not set to true
            if($empty == FALSE) {
                // try to delete the now empty directory
                if(!rmdir($directory)) {
                    // return false if not possible
                    return FALSE;
                }
            }
            // return success
            return TRUE;
        }
    }
    
    /**
     * copies files and non-empty directories
     * @param string $src
     * @param string $dst
     */
    protected function _rcopy($src, $dst) {
        if (is_dir($src)) {
            if(!file_exists($dst)) {
                mkdir($dst);
            }
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file != "." && $file != ".." && $file != '.DS_Store') {
                    $this->_rcopy("$src/$file", "$dst/$file");
                }
            }
        }
        else if (file_exists($src)) {
            copy($src, $dst);
        }
    }
    
     /**
     * Read all files - not folders
     *
     * @param string $directory
     * @param array $list
     * @return boolean
     */
    private function _rlistFiles($directory, &$list) {

        $result = opendir($directory);
        if ($result === FALSE) {
            return false;
        }
        clearstatcache();

        while (false !== ($file = readdir($result))) {
            if($file == '.' || $file == '..' || $file == '.DS_Store') {
                continue;
            }
            $path = $directory . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $list[] = $path;
            } elseif (is_dir($path)) {
                $this->_rlistFiles($path, $list);
            }
        }
        closedir($result);
        //sort($list);
        return true;
    }
    
    /**
     * Unzip file
     *
     * @param string $path
     * @return boolean
     */
    public function unzip($path, $newDir = false) {
        $filePath = $this->getRealPath($path);
        if (!file_exists($filePath)) {
            $this->returnError('File does not exists ' . $path);
            return false;
        }
        // get the absolute path to $file
        $dir = pathinfo($filePath, PATHINFO_DIRNAME);
        $packageName = pathinfo($filePath, PATHINFO_FILENAME);
        if($newDir){
            $dir .= DIRECTORY_SEPARATOR . $packageName;
        }        

        $zip = new ZipArchive;
        $res = $zip->open($filePath);
        if(!$res) {
            $this->returnError('Cannot extract file');
            return false;
        }
        $tmpDir = APPLICATION_PATH . '/../tmp/' . session_id() . '-' .$packageName;
        $zip->extractTo($tmpDir);
        $zip->close();
        //check size
        $extractSize = $this->_helper->getDirSize($tmpDir);
        if($extractSize > $this->_helper->getFreeSpace()) {
            $this->_rrmdir($tmpDir);
            $this->returnError('Not enough space on disk');
            return false;
        }
        $files = array();
        $this->_rlistFiles($tmpDir, $files);
        $mimeValidator = new Zend_Validate_File_MimeType($this->_helper->getDefaultMimeTypes());
        $extValidator = new Zend_Validate_File_Extension($this->_helper->getDefaultExtensions());
        foreach ($files as $currFile) {
            if(!$extValidator->isValid($currFile)) {
                $this->_rrmdir($tmpDir);
                $this->returnError(sprintf('File [%s] has invalid extension.',basename($currFile)));
                return false;
            }
            if(!$mimeValidator->isValid($currFile)) {
                $this->_rrmdir($tmpDir);
                $this->returnError(sprintf('File [%s] has invalid mime type.',basename($currFile)));
                return false;
            }
        }
        $this->_rcopy($tmpDir, $dir);
        $this->_rrmdir($tmpDir);
        return true;
    }
}