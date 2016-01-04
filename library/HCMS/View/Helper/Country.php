<?php
/**
 * View helper for country manipulation
 *
 * @package HCMS
 * @subpackage Controller
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_Country extends Zend_View_Helper_Abstract {
    
    
    protected $_settings = array();
    
    /**
     * Get Country helper
     *
     */
    public function country(array $settings) {
        $this->_settings = $settings;
        return  $this;
    }
    
    /**
     * Get country list
     * 
     * @return array
     */
    public function getList($lang = null){
        if(!$lang){
            $lang = CURR_LANG;
        }
        $settings = $this->_settings;
        $countries = Application_Model_CountryMapper::getInstance()->getAllCountries($lang);                 
        
        if(isset($settings['selected_only']) && $settings['selected_only']){
            $result = array();
            foreach($countries as $country){
                if(in_array($country->get_code2(), $settings['selected_countries'])){
                    $result[] = $country;
                }
            }
            return $result;            
        } else{
            if(isset($settings['selected_countries']) && count($settings['selected_countries'])){
                $rest_countries = array();
                $selected_countries = array();                
                foreach($countries as $country){
                    if(!in_array($country->get_code2(), $settings['selected_countries'])){
                        $rest_countries[$country->get_code2()] = $country;
                    }
                    if(in_array($country->get_code2(), $settings['selected_countries'])){
                        $selected_countries[$country->get_code2()] = $country;
                    }
                }
                
                $selected_countries = $this->sortSelected($selected_countries, $settings['selected_countries']);
                $spacer = new Application_Model_Country();
                $spacer->set_code2('')
                        ->set_name("--------------------");
                $selected_countries[] = $spacer;
                return array_merge($selected_countries, $rest_countries);                
            } else {
                return $countries;
            }
        }      
    }
    
    private function sortSelected($selected_countries, $settings){
        $tmpcoutries = array(); 
        foreach($settings as $code){
            foreach($selected_countries as $k => $v){
                if($code == $k){
                    $tmpcoutries[$code] = $v;
                }
            }
        }
        return $tmpcoutries;
    }
    
    /**
     * Set default value
     * 
     * @param string $dataCountryKey
     * @return mixed 
     */
    public function setDefault($dataCountryKey = 'country'){
        $emailParams = $this->_settings;
        $request =  Zend_Controller_Front::getInstance()->getRequest();
        if($request->isPost() || !isset($emailParams['ip_country_detection']) || !$emailParams['ip_country_detection']){
            return;
        }
        $ip = HCMS_Utils::getRealIpAddr(); 
        $myCountry = new Application_Model_Country();
        if(Application_Model_CountryMapper::getInstance()->getCountryByGeoIp($ip, $myCountry)){
            $this->view->data[$dataCountryKey] = $myCountry->get_code2();
        }        
    }    
}