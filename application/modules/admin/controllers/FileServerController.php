<?php
/**
 * Fileserver JSON RPC Controller
 *
 * @package Modules
 * @subpackage Admin
 * @copyright Horisen
 * @author milan
 */
class Admin_FileServerController extends HCMS_Controller_Action_Admin {
    
    protected function _initLayout() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        parent::_initLayout();
    }

    public function indexAction() {
        // Instantiate server, etc.
        $server = new Zend_Json_Server();
        Zend_Registry::set("fileserver_helper", $this->_fileHelper);
        $server->setClass('Admin_Model_FileServer');

        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            // Indicate the URL endpoint, and the JSON-RPC version used:
            $server ->setTarget('/file-server/')
                    ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

            // Grab the SMD
            $smd = $server->getServiceMap();

            // Return the SMD to the client
            header('Content-Type: application/json');
            echo $smd;
            die();
        }

        $server->handle();
        die();
    }

    /**
     *Upload File
     *
     */
    public function uploadAction() {
        $dir = $this->getRequest()->getParam("dir");

        $uploadSettings = $this->_application->get_settings('upload');
        $defaultUploadSettings = $this->_application->get_settings('default_upload');
        $newKeys = array('extensions', 'mimetypes');
        $defaultUploadSettings = $this->newKeys($defaultUploadSettings, $newKeys);
        
        if(!isset ($uploadSettings)){
            $uploadSettings = $defaultUploadSettings;
        } else {
            $uploadSettings = array_merge_recursive($uploadSettings, $defaultUploadSettings);
        }
        $uploadSettings = array_merge($uploadSettings, array(
            'dir'   => $dir,
            'field' => 'file_upload'
        ));

        $result = $this->_fileHelper->upload($uploadSettings);
        if($result === false) {
            echo json_encode(array(
                    'success'   => false,
                    'message'   => $this->_fileHelper->getLastErrorMessage()
            ));

        }
        else{
            $result = array(
                'success'=> true,
                'path'   => $result
            );
            $lastError = $this->_fileHelper->getLastErrorMessage();
            if($lastError != ''){
                $result['message'] = $lastError;
            }
            echo json_encode($result);
        }
        die;
    }
    
    public function pasteAction(){
        $clipboard = $this->getRequest()->getParam("clipboardPath");
        $destination = $this->getRequest()->getParam("path");
        $clipboardPath = $this->_fileHelper->getPath($clipboard);
        $destinationPath = $this->_fileHelper->getPath($destination);
        $destinationType = $this->getRequest()->getParam("type");
        $active_module = $this->getRequest()->getParam("active_module");
        $clipboardType = $this->getRequest()->getParam("clipboardType");
        $freeSpace =  $this->_fileHelper->getFreeSpace();
        if($destinationType != "dir" || !is_dir($destinationPath['real'])){
            return $this->_helper->json(array(
                'success'   => false,
                'message'   => "You can not copy there."
            )); 
        }
        switch ($clipboardType) {
            case "dir":
                $clipboardSize = $this->_fileHelper->getDirSize($clipboardPath['real']);
                if($clipboardSize <=  $freeSpace){
                    $dir = $this->getDestinationPathDir($clipboard, $destination);
                    mkdir($this->_fileHelper->getRoot().$dir, 0777);
                    exec("cp -r ". $clipboardPath['real'] ."/* ".$this->_fileHelper->getRoot().$dir);
                    $success = true;
                    $message = "Copied."; 
                }else{
                    $success = false;
                    $message = "Insufficient free space."; 
                }
            break;
            case "file":
                $clipboardSize = exec("stat -c %s ".$clipboardPath['real']);//filesize($clipboardPath['real']);
                if($clipboardSize <=  $freeSpace){
                    $destinationPathFile = $this->getDestinationPathFile($destinationPath['real'], basename($clipboardPath['real']));
                    if(copy($clipboardPath['real'], $destinationPathFile)){
                        $success = true;
                        $message = "Copied.";
                    }else{
                        $success = false;
                        $message = "Error!";
                    };
                }else{
                    $success = false;
                    $message = "Insufficient free space."; 
                }
            break;
            default:
                break;
        }
        return $this->_helper->json(array(
            'success'   => $success,
            'message'   => $message
        ));
    }
    /**
     * Get Destination path file
     *  
     * @param string $destinationPath
     * @param string $fileName
     * @param int $i
     * @param string $newFileName
     * @return string 
     */
    protected function getDestinationPathFile($destinationPath, $fileName, $i = 1, $newFileName = ""){
        
        $destinationPathFile = $destinationPath."/".$fileName;
        if($newFileName != ""){
            $destinationPathFile = $destinationPath."/".$newFileName;
        }
       
        $destinationPathInfo = pathinfo($destinationPath."/".$fileName);
        
        if(is_file($destinationPathFile)){
            $newFileName = $destinationPathInfo['filename']."_".$i.".".$destinationPathInfo['extension'];
            $i++;
            $destinationPathFile = $this->getDestinationPathFile($destinationPath, $fileName, $i, $newFileName);
        }
        return $destinationPathFile;
    }
    
    /**
     * Get Destination dir
     *  
     * @param string $clipboard
     * @param string $destination
     * @param int $i
     * @param string $clipboardNew
     * @return string 
     */
    protected function getDestinationPathDir($clipboard, $destination, $i = 1, $clipboardNew = ""){
        $clipboardArray = explode("/", $clipboard);
        $destinationDir = (($destination != "/")?$destination:"")."/".end($clipboardArray);
        if($clipboardNew != ""){
            $clipboardNewArray =  explode("/", $clipboardNew);
            $destinationDir = (($destination != "/")?$destination:"")."/".end($clipboardNewArray);
        }
        $destinationPath  = $this->_fileHelper->getPath($destinationDir);
        if(is_dir($destinationPath['real'])){
            $clipboardArray[count($clipboardArray)-1] = end($clipboardArray)."_".$i;
            $clipboardNew = join("/",$clipboardArray);
            $i++;
            $destinationDir = $this->getDestinationPathDir($clipboard, $destination, $i, $clipboardNew);
        }
        return $destinationDir;
    }

    /**
     * Load image resource
     *
     * @param string $imagePath
     * @return resource | null
     */
    private function imageFactory($imagePath){
        $pathInfo = pathinfo($imagePath);
        $extension = strtolower($pathInfo['extension']);

        switch ($extension) {
            case "jpg":
            case "jpeg":
                return imagecreatefromjpeg($imagePath);
            case "png":
                return imagecreatefrompng($imagePath);
            case "gif":
                return imagecreatefromgif($imagePath);
            default:
                return null;
        }
    }

    /**
     * Save image resource
     *
     * @param resource $imageRes
     * @param string $imagePath
     * @param int $quality
     * @return boolean
     */
    private function imageSave($imageRes,$imagePath,$quality){
        $pathInfo = pathinfo($imagePath);
        $extension = strtolower($pathInfo['extension']);

        switch ($extension) {
            case "jpg":
            case "jpeg":
                return imagejpeg($imageRes, $imagePath, $quality);
            case "png":
                return imagepng($imageRes, $imagePath);
            case "gif":
                return imagegif($imageRes, $imagePath);
            default:
                return false;
        }
    }

    /**
     * Process Image
     *
     */
    public function processImageAction(){

        $relativePath = $this->getRequest()->getParam("sourcePath");
        $paths = $this->_fileHelper->getPath($relativePath);
        if($paths === false){
            return $this->_helper->json(array(
                    'success'   => false,
                    'message'   => "Invalid file [$relativePath]"
            ));
        }

        $imageQuality = 90;
        $imagePath = $paths['real'];

        $origInfo = getimagesize($imagePath);
        $origWidth = $origInfo[0];
        $origHeight = $origInfo[1];
        //load image resource
        $sourceRes = $this->imageFactory($imagePath, $imageQuality);
        if(!$sourceRes){
            return $this->_helper->json(array(
                    'success'   => false,
                    'message'   => "Invalid file format [$filePath]"
            ));
        }
        //get measures
        //destination
        $destinationWidth = $this->getRequest()->getParam('destinationWidth');
        $destinationHeight = $this->getRequest()->getParam('destinationHeight');
        $destinationX = $this->getRequest()->getParam('destinationX');
        $destinationY = $this->getRequest()->getParam('destinationY');
        //source dims - if not specified use original dims
        $sourceWidth = $this->getRequest()->getParam('sourceWidth');
        $sourceHeight = $this->getRequest()->getParam('sourceHeight');
        $sourceX = $this->getRequest()->getParam('sourceX');
        $sourceY = $this->getRequest()->getParam('sourceY');
        //create new image resource
        $destinationRes = ImageCreateTrueColor($destinationWidth, $destinationHeight);
        //resample image
        $this->_log("imagecopyresampled(dst_x: $destinationX, dst_y: $destinationY, src_x: $sourceX, src_y: $sourceY, dst_w: $destinationWidth, dst_h: $destinationHeight, src_w: $sourceWidth, src_h: $sourceHeight);", Zend_Log::INFO);
        imagecopyresampled($destinationRes, $sourceRes,
                $destinationX, $destinationY,
                $sourceX, $sourceY,
                $destinationWidth, $destinationHeight,
                $sourceWidth, $sourceHeight);
        //save image
        if($this->imageSave($destinationRes, $imagePath, $imageQuality)){
            return $this->_helper->json(array(
                    'success'   => true,
                    'message'   => "Image processed"
            ));
        }
        else{
            return $this->_helper->json(array(
                    'success'   => false,
                    'message'   => "Error saving image"
            ));
        }
    }
    
     /**
     * Function for changing array key names
     *
     * @param array $existing
     * @param array $newKeys
     * @return array 
     */
    function newKeys($existing, $newKeys) {
        // a really simple check that the arrays are the same size
        if (count($existing) !== count($newKeys))
            return false; // or pipe out a useful message, or chuck exception

        $data = array();  // set up a return array
        $i = 0;
        foreach ($existing as $k => $v) {
            $data[$newKeys[$i]] = $v;  // build up the new array
            $i++;
        }
        return $data; // return it
    }

}