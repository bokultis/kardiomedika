<?php
/**
 * Action helper for country manipulation
 *
 * @package HCMS
 * @subpackage Controller
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_Controller_Action_Helper_Country extends Zend_Controller_Action_Helper_Abstract {
    
    
    protected $_settings = array();
    
    /**
     * Get Country helper
     *
     */
    public function setSettings(array $settings) {
        $this->_settings = $settings;
        return  $this;
    }
    
    /**
     * Get country list
     * 
     * @return array
     */
    public function getList(){
        $settings = $this->_settings;
        $countries = Application_Model_CountryMapper::getInstance()->getAllCountries(CURR_LANG);                
        if(!isset($settings['default_country']) || $settings['default_country'] != 'no'){
            return $countries;
        }   
        $rest_countries = array();
        $selected_countries = array();            
        if(isset($settings['default_country_on_top']) && $settings['default_country_on_top'] == 'no'){
            foreach($countries as $country){
                if(!in_array($country->get_code2(), $settings['selected_countries'])){
                    $rest_countries[] = $country;
                }
                if(in_array($country->get_code2(), $settings['selected_countries'])){
                    $selected_countries[] = $country;
                }
            }
            return array_merge($selected_countries, $rest_countries);
        }else{
            $result = array();
            foreach($countries as $country){
                if(in_array($country->get_code2(), $settings['selected_countries'])){
                    $result[] = $country;
                }
            }
            return $result;
        }        
    }
    
    /**
     * Set default value
     * 
     * @param string $dataCountryKey
     * @return mixed 
     */
    public function setDefault($dataCountryKey = 'country'){
        $emailParams = $this->_settings;
        if($this->_actionController->getRequest()->isPost() || !isset($emailParams['ip_country_detection']) || $emailParams['ip_country_detection'] != 'yes'){
            return;
        }
        $ip = HCMS_Utils::getRealIpAddr(); 
        $myCountry = new Application_Model_Country();
        if(Application_Model_CountryMapper::getInstance()->getCountryByGeoIp($ip, $myCountry)){
            $this->_actionController->view->data[$dataCountryKey] = $myCountry->get_code2();
        }        
    }    
}