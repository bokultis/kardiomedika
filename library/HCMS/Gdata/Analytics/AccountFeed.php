<?php

/**
 * @category   HCMS
 * @package    Gdata
 * @subpackage Analytics
 */
class HCMS_Gdata_Analytics_AccountFeed extends Zend_Gdata_Feed
{
    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'HCMS_Gdata_Analytics_AccountEntry';

    /**
     * The classname for the feed.
     *
     * @var string
     */
    protected $_feedClassName = 'HCMS_Gdata_Analytics_AccountFeed';

    /**
     * @see Zend_GData_Feed::__construct()
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(HCMS_Gdata_Analytics::$namespaces);
        parent::__construct($element);
    }
}
