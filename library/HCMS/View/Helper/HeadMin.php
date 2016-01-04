<?php
/**
 * HeadLink or headScript bundling and minification
 *
 * Usage - in main layout add:
 *
 *        $minifyOptions = array(
 *            'public_dir'    => APPLICATION_PATH . '/../public',
 *            'content_dir'   => APPLICATION_PATH . '/../public/content',
 *            'content_web'   => '/content',
 *            'disabled'      => false,
 *            'minify_cmd'    => 'java -jar /opt/java/yuicompressor.jar -o "%file_path%" "%file_path%"'
 *        );
 *        echo $this->headMin($this->headLink(),$minifyOptions);
 *        echo $this->headMin($this->headScript(),$minifyOptions);
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_HeadMin extends Zend_View_Helper_Placeholder_Container_Abstract {

    private $_publicDir = null;
    private $_contentDir = null;
    private $_contentWeb = null;
    private $_minifyCmd = null;
    private $_version = null;

    private $_type = null; //"link" or "script"

    /**
     * Get files hash id
     * 
     * @param array $items
     * @return string
     */
    private function _calculateHash(array $items){
        $id = '';
        if(isset ($this->_version)){
            $id = $this->_version;
        }        
        foreach ($items as $item) {
            $id .= '-' . $item['id'];
        }
        return md5($id);
    }

    /**
     * Execute minification standalone program
     * 
     * @param string $filePath
     * @return
     */
    private function _execMinifyCmd($filePath){
        if(!isset ($this->_minifyCmd)){
            return false;
        }
        $cmd = str_replace("%file_path%", $filePath, $this->_minifyCmd);
        //echo "<br>" . $cmd . "<br>";
        $result = system($cmd);
        if($result !== false){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    /**
     * Return file extension
     * 
     * @return string
     */
    private function _getCacheExt(){
        return ($this->_type == 'link')?".css":".js";
    }

    /**
     * Get container item desciption id, content, path
     * 
     * @param stdClass $item
     * @return array
     */
    private function _getItemDesc($item){
        if($this->_type == 'script'){
            if(!empty ($item->source)){
                return array(
                    'id'        => $item->source,
                    'content'   => $item->source
                );
            }
            elseif(isset ($item->attributes) && isset ($item->attributes['src'])){
                return array(
                    'id'        => $item->attributes['src'],
                    'path'   => $item->attributes['src']
                );
            }
        }
        else{            
            if(!empty ($item->rel) && $item->rel == 'stylesheet' && !empty ($item->type) && $item->type == 'text/css'){
                return array(
                    'id'        => $item->href,
                    'path'   => $item->href
                );
            }
        }
        return false;
    }

    /**
     * Main view helper function
     * 
     * @param Zend_View_Helper_Placeholder_Container_Standalone $headScript
     * @param array $options
     * @return Zend_View_Helper_Placeholder_Container_Standalone
     */
    public function headMin(Zend_View_Helper_Placeholder_Container_Standalone $headScript, array $options) {
        //read all options
        if(isset ($options['disabled']) && $options['disabled']){
            return $headScript;
        }
        if($headScript instanceof Zend_View_Helper_HeadScript){
            $this->_type = 'script';
        }
        else if($headScript instanceof Zend_View_Helper_HeadLink){
            $this->_type = 'link';
        }
        else{
            throw new Zend_Exception("$headScript must be Zend_View_Helper_HeadScript or Zend_View_Helper_HeadLink");
        }
        if(isset ($options['public_dir'])){
            $this->_publicDir = $options['public_dir'];
        }
        if(isset ($options['content_dir'])){
            $this->_contentDir = $options['content_dir'];
        }
        if(isset ($options['content_web'])){
            $this->_contentWeb = $options['content_web'];
        }
        if(isset ($options['minify_cmd'])){
            $this->_minifyCmd = $options['minify_cmd'];
        }
        if(isset ($options['version'])){
            $this->_version = $options['version'];
        }
        //get items for minification
        $items = array();
        $headScript->getContainer()->ksort();
        $unsetKeys = array();
        foreach ($headScript as $key => $item) {
            //Zend_Debug::dump($item);
            $itemDesc = $this->_getItemDesc($item);
            if($itemDesc === false){
                continue;
            }            
            //include only local paths
            if(!isset ($itemDesc['path']) || (substr($itemDesc['path'], 0, 7) != 'http://' && substr($itemDesc['path'], 0, 8) != 'https://')){
                $items[] = $itemDesc;
                $unsetKeys[] = $key;
            }
        }
        //unset items to be minified
        foreach ($unsetKeys as $key) {
            unset($headScript[$key]);
        }     
        $cacheId = $this->_calculateHash($items);
        $cacheFileName = $this->_contentDir . '/' . $cacheId . $this->_getCacheExt();    
        if(!file_exists($cacheFileName)){
            //create minified cache file
            $content = '';
            foreach ($items as $key => $itemDesc) {
                if(isset ($itemDesc['content'])){
                    $content .= PHP_EOL . $itemDesc['content'];
                }
                elseif(isset ($itemDesc['path'])){
                    $content .= PHP_EOL . file_get_contents($this->_publicDir . '/' . $itemDesc['path']);
                }
            }
            //save bundled content
            file_put_contents($cacheFileName, $content);
            //minify
            if(isset ($this->_minifyCmd)){
                $this->_execMinifyCmd($cacheFileName);
            }
        }        
        //append cache file
        $cacheFileWeb = $this->_contentWeb . '/' . $cacheId . $this->_getCacheExt();
        //print_r($headScript->getContainer());
        if($this->_type == 'script'){
            $headScript->appendFile($cacheFileWeb);
        }
        else{
            $headScript->appendStylesheet($cacheFileWeb);
        }
        return $headScript;
    }
}