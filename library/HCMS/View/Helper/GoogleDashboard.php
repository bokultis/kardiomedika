<?php
/**
 * View helper to enable and setup google dashboard
 * 
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author boris
 *
 */
class HCMS_View_Helper_GoogleDashboard extends Zend_View_Helper_Abstract {
    
    protected static $active = null;
    protected  $app = null;
    protected  $clientId = null;
    
    public function isActive(){
        if(isset(self::$active)){
            return self::$active;
        }

        //check
        $settings = $this->app->get_settings('tags');
        if(!isset ($this->clientId) || $this->clientId == '' /* || !isset($settings['ga']['active']) || $settings['ga']['active'] != true*/){
            self::$active = false;
        } else {
            self::$active = true; 
        }
        return self::$active;
        //more check TODO        
    }
    
    /**
     * Inititilaize
     * 
     * @return string 
     */
    public function init($clientId = ''){
        
        
        if(!isset($this->app) && isset($this->view->application)){
            $this->app = $this->view->application;
        }
        
        if(!isset($this->app)){
            return false;
        }
        
        $this->clientId = isset($this->view->clientId) && $this->view->clientId != '' ? $this->view->clientId : '';  
        $this->isActive();
        if(!self::$active){
            return false;
        }
        $settings = $this->app->get_settings('tags');
        
        $viewId = isset($settings['ga']['view_id']) && $settings['ga']['view_id'] != '' ? $settings['ga']['view_id'] : '';
        
        return "
<script type='text/javascript'>
    gapi.analytics.ready(function() {
        gapi.analytics.auth.authorize({
            container: 'view-selector',
            clientid: '" . $clientId . "'
        });
    });
    
    var viewId = '" . $viewId . "';
</script>    
";
       
        //$this->view->headLink()->prependStylesheet('/modules/admin/css/bootstrap-grid.css');
    }    
    /**
     * Google dashboard 
     *  
     */
    public function googleDashboard() {        
        return $this;
    }    
 
}
