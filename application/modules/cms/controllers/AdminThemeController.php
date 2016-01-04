<?php
/**
 * Theme controller
 *
 * @package Cms
 * @subpackage Controllers
 * @copyright Horisen
 * @author Ilija Petkovic
 */

class Cms_AdminThemeController extends HCMS_Controller_Action_Admin{
    private $_themeDir = "../themes/";
    
    public function init(){
        parent::init();
    }
    
    public function themeAction(){
        $themes = $this->_findThemes();
        foreach($themes as $theme){
            $this->_createSymlinks($theme);
        }
        $this->view->themes = $this->_getMetaJson($themes);
    }
    
    public function activateAction(){
        //Get theme name
        $theme = $this->_getParam('theme');
        
        // Update database
        $mapper = Application_Model_ApplicationMapper::getInstance();
        $appSettings = $mapper->fetchAll();
        $settings = $appSettings[0]->get_settings();
        $settings['theme'] = $theme;
        $app = new Application_Model_Application();
        $app->set_settings($settings);
        $app->set_id(1);        
        $mapper->save($app);
        
        //Create symlinks
        //$this->_createSymlinks($theme);
        
        //Delete cache
        HCMS_Cache::getInstance()->getObjectCache(Application_Model_TranslateMapper::getInstance())->clean();
        HCMS_Cache::getInstance()->getCoreCache()->clean();
        
        //Return json
        $this->_helper->json(array('message' => $this->translate("Theme is activated"), 'success' => true));   
    }
    
    /*
     * Theme edit page
     */
    public function themeEditAction(){
        $param = $this->_getParam('theme');
        $this->view->theme = $param;
        //$this->_checkSelectors($param);
        $mapper = Application_Model_ApplicationMapper::getInstance();
        $appSettings = $mapper->fetchAll();
        $settings = $appSettings[0]->get_theme_settings();
        if(isset($settings[$param])){            
            $this->view->customCss = $settings[$param]['css'];
            $this->view->customCssJson = json_encode($settings[$param]['json']);
        }else{
            $this->view->customCSS = '';
            $this->view->customCssJson = json_encode(array());
        }
        
        $this->view->editorProperties = $this->_getPropertiesJson($param);        
    }
    
    /*
     * Theme edit submit
     */
    public function submitEditAction(){
        // Disable layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        //Get params from ajax call
        $theme = $this->_getParam('theme');
        $css = $this->_getParam('css');
        $css = str_replace("\n", "", $css);
        
        //Update database
        $mapper = Application_Model_ApplicationMapper::getInstance();
        $appSettings = $mapper->fetchAll();
        $settings = $appSettings[0]->get_theme_settings();
        $settings[$theme]['css'] = $css;
        $settings[$theme]['json'] = $this->_getParam('cssJson');
        
        $app = new Application_Model_Application();
        $app->set_theme_settings($settings);
        $app->set_id(1);
        $mapper->save($app);
        
        echo $this->translate("Successfully edited");
        $this->view->customCss = str_replace("\n", "", $settings[$theme]['css']);
        
        $this->view->customCssJson = json_encode($settings[$theme]['json']);
    }
    
    /*
     * Revert default css for theme
     */
    public function revertDefaultAction(){
        // Disable layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        //Get params from ajax call
        $theme = $this->_getParam('theme');
        
        //Update database
        $mapper = Application_Model_ApplicationMapper::getInstance();
        $appSettings = $mapper->fetchAll();
        $settings = $appSettings[0]->get_theme_settings();
        
        //Zend_Debug::dump(isset($settings[$theme]));
        if(isset($settings[$theme])){
            $settings[$theme]['css'] = '';
            $settings[$theme]['json'] = '';
        }
        
        $app = new Application_Model_Application();
        $app->set_theme_settings($settings);
        $app->set_id(1);
        $mapper->save($app);
       
        echo $this->translate("Successfully reverted");
        $this->view->customCss = str_replace("\n", "", $settings[$theme]['css']);
        $this->view->customCssJson = json_encode($settings[$theme]['json']);
        $this->view->theme = $theme;
        
    }    
    
    /*
     * Parse meta.json file from theme folder
     * 
     * @param $themes array of themes
     * @return array
     */
    private function _getMetaJson($themes){
        $jsonParsed = array();
        $applicationSettings = Application_Model_ApplicationMapper::getInstance()->fetchAll();
        $activeTheme = $applicationSettings[0]->get_settings('theme');
        $themesDir = $this->_findThemes();
        $i=0;
        foreach($themes as $theme){
            if($this->_checkMeta($this->_themeDir.$theme, 'meta')){
                $getFile = file_get_contents($this->_themeDir.$theme."/meta.json", true);
                $jsonParsed[$i] = json_decode($getFile, true);
                if($activeTheme == $themesDir[$i])
                    $jsonParsed[$i]['activated'] = "active";
                else
                    $jsonParsed[$i]['activated'] = "";
                $jsonParsed[$i]['db_name'] = $themesDir[$i];
                $i++;
            }
        }
        return $jsonParsed;
    }
    
    /*
     * Get editor-properties.json file
     * 
     * @param $theme active theme
     * @return json object
     */
    private function _getPropertiesJson($theme){
        if($this->_checkMeta($this->_themeDir.$theme, 'editor-properties')){
            $getFile = file_get_contents($this->_themeDir.$theme."/editor-properties.json", true);
            return $getFile;
        }
    }
    
    /*
     * Check if meta.json exist
     * @return bool 
     */
    private function _checkMeta($path, $filename){
        return file_exists($path."/".$filename.".json");
    }
    
    /*
     * Find themes in themes dir
     * @return array of themes
     */
    private function _findThemes(){        
        //Find all themes in themes dir,
        //Remove current and parrent dirs (dots),
        //Reset index keys in array
        return array_values(array_diff(scandir($this->_themeDir), array('..', '.')));        
    }
    
    /*
     * Create symlinks for theme if not exists
     */
    private function _createSymlinks($theme){
        $bootstrap = $this->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        $publicDirectory = (isset($config['default']['publicDirectory']))? $config['default']['publicDirectory'] : APPLICATION_PATH .'/../public';
        $target = APPLICATION_PATH . '/../themes/' . $theme . '/public';
        $symlink = $publicDirectory . '/themes/' . $theme;
        
        if(!is_dir($symlink)){
            return;
        }
        if(!is_link($symlink) && !is_dir($symlink)){
           symlink($target, $symlink);
        }
    }
    
    /*
     * Check selectors between database and editor
     */
    private function _checkSelectors($theme){
        $mapper = Application_Model_ApplicationMapper::getInstance();
        $appSettings = $mapper->fetchAll();
        $settings = $appSettings[0]->get_theme_settings();
        if(isset($settings[$theme])){
            $editorProperties = json_decode($this->_getPropertiesJson($theme), true)['pages']['/themes/'.$theme.'/theme_edit/preview/complex.html']['def'];
            $dbJson = isset($settings[$theme]['json']['/themes/'.$theme.'/theme_edit/preview/complex.html'])?$settings[$theme]['json']['/themes/'.$theme.'/theme_edit/preview/complex.html']:'';
            $dbCss = $settings[$theme]['css'];
            $flag = false;
            if(is_array($dbJson)){
                foreach($dbJson as $property => $value){
                    if(!array_key_exists($property, $editorProperties)){
                        unset($dbJson[$property]);
                        preg_replace("/".$property."{(.*?)}/", " ", $dbCss);
                        $flag = true;
                    }
                }  
            }                      
        }
        if($flag){
            $settings[$theme]['css'] = $dbCss;
            $settings[$theme]['json'] = $dbJson;
            $app = new Application_Model_Application();
            $app->set_theme_settings($settings);
            $app->set_id(1);
            $mapper->save($app);
        }        
    }
    
}