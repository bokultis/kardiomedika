<?php

/**
 * Request CLI
 *
 * @package HCLI
 * @subpackage Controller
 * @author milan
 * 
 */
class HCLI_Controller_Request_Cli extends Zend_Controller_Request_Abstract {
    /**
     *
     * @var Zend_Console_Getopt
     */
    protected $_getopt = null;

    public function __construct(Zend_Console_Getopt $getopt) {
        $this->_getopt = $getopt;
        $getopt->parse();
        if ($getopt->{$this->getModuleKey()}) {
            $this->setModuleName(HCLI_Controller_Util::decode($getopt->{$this->getModuleKey()}));
        }
        if ($getopt->{$this->getControllerKey()}) {
            $this->setControllerName(HCLI_Controller_Util::decode($getopt->{$this->getControllerKey()}));
        }
        if ($getopt->{$this->getActionKey()}) {
            $this->setActionName($getopt->{$this->getActionKey()});
        }
    }

    /**
     *
     * @return Zend_Console_Getopt
     */
    public function getCliOptions() {
        return $this->_getopt;
    }
}
