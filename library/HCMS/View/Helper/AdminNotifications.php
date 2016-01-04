<?php
/**
 * Get admin notification messages
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_AdminNotifications extends Zend_View_Helper_Abstract
{
    
    /**
     * Ini config
     * 
     * @var array
     */
    private $config = array();
    
    /**
     * Get BS option
     * @param string $name
     * @param string $section
     * @return null|mixed
     */
    protected function _getBootstrapOption($name, $section = 'default', $defaultValue = null){
        if(isset($this->config[$section][$name])){
            return $this->config[$section][$name];
        }
        return $defaultValue;
    }    

    /**
     * Get array of notification messages
     *
     * @param array $admin
     * @return array
     */
    public function adminNotifications(Auth_Model_User $admin)
    {
        $result = array();
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $this->config = $bootstrap->getOptions();
        
        $daySeconds = 3600 * 24;
        
        $expire_password = strtotime($admin->get_changed_password_dt()) + $daySeconds * $this->_getBootstrapOption('expire_password_day', 'default', 90);
        if ( $expire_password < time() + 30 * $daySeconds) {
            if($expire_password < time()) {
                $result['expire_password'] = $this->view->translate('Your password expired. Please update.');
            } else {
                $result['expire_password'] = strtr($this->view->translate('Your password expires in {days} days. Please update.'), array(
                    '{days}' => floor(($expire_password - time()) / $daySeconds)
                ));                
            }
        }
        return $result;
    }      
}
