<?php
/**
 * View helper which sets Google search js
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author boris
 * 
 */
class HCMS_View_Helper_GoogleSearchCode extends Zend_View_Helper_Abstract {
    
    /**
     * Get Google Search code
     *
     * @param Application_Model_Application $app
     */
    public function GoogleSearchCode ($app, $ReturnCss = true) {
        $gsSettings = $app->get_settings('gsc');
        $html = '';
        if(!isset ($gsSettings) || $gsSettings['cx'] == '' || !isset($gsSettings['active']) || $gsSettings['active'] == false){
            return '';
        } 
        
        if($ReturnCss){
            echo $this->prepareCss($gsSettings);
        }
        
        $html = "
            <div id='search'>
                <script>
                (function() {
                    var cx = '".$gsSettings['cx']."';
                    var gcse = document.createElement('script');
                    gcse.type = 'text/javascript';
                    gcse.async = true;
                    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
                        '//www.google.com/cse/cse.js?cx=' + cx;
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(gcse, s);
                })();
                </script>
            
            ";

        $html .= "<gcse:searchbox-only "
                . "resultsUrl='". $this->view->serverUrl() ."/". $this->view->currLang ."/search' "
                . "lr='lang_".$this->view->currLang."' enableAutoComplete='true'>"
                . "</gcse:searchbox-only>\n</div>";
        
        
        $html .= "<span id='searchToggle' class='fa fa-search'></span>"; 

        echo  $html;
    }
    
    /**
     * Convert json to css
     * @param type $gsSettings
     * @return string
     */
    private function prepareCss($gsSettings){
        $search_css = "";
        if(isset ($gsSettings['css']) && count($gsSettings['css'])){
            $search_css .= "<style type='text/css'>\n";
            foreach($gsSettings['css'] as $selector => $props){

                $search_css .= "".$selector."{";
                    foreach($props as $p => $v){
                        $search_css .= "".$p.": " . $v . " !important; ";
                    }
                $search_css .= "}" . PHP_EOL;
            }
            $search_css .= "</style>" . PHP_EOL;
        }

        return $search_css;
    }
}