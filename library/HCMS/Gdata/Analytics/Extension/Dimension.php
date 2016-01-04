<?php

/**
 * @category   HCMS
 * @package    Gdata
 * @subpackage Analytics
 */
class HCMS_Gdata_Analytics_Extension_Dimension
    extends HCMS_Gdata_Analytics_Extension_Metric
{
    protected $_rootNamespace = 'ga';
    protected $_rootElement = 'dimension';
    protected $_value = null;
    protected $_name = null;
}
