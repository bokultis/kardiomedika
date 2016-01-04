<?php

/**
 * @category   HCMS
 * @package    Gdata
 * @subpackage Analytics
 */
class HCMS_Gdata_Analytics_AccountEntry extends Zend_Gdata_Entry
{
	protected $_accountId;
	protected $_accountName;
	protected $_profileId;
	protected $_webPropertyId;
	protected $_currency;
	protected $_timezone;
	protected $_tableId;

	/**
	 * @see Zend_Gdata_Entry::__construct()
	 */
	public function __construct($element = null)
    {
        $this->registerAllNamespaces(HCMS_Gdata_Analytics::$namespaces);
        parent::__construct($element);
    }

    /**
     * @param DOMElement $child
     * @return void
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName){
        	case $this->lookupNamespace('ga') . ':' . 'property';
	            $property = new HCMS_Gdata_Analytics_Extension_Property();
	            $property->transferFromDOM($child);
	            $this->{$property->getName()} = $property;
            break;
        	case $this->lookupNamespace('ga') . ':' . 'tableId';
	            $tableId = new HCMS_Gdata_Analytics_Extension_TableId();
	            $tableId->transferFromDOM($child);
	            $this->_tableId = $tableId;
            break;
        	default:
            	parent::takeChildFromDOM($child);
            break;
        }
    }
}
