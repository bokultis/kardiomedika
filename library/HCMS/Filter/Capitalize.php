<?php
/**
 * Capitalize Filter
 *
 * @package HCMS
 * @subpackage Filter
 * @copyright Horisen
 * @author milan
 */


class HCMS_Filter_Capitalize implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, converting characters to uppercase as necessary
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        return mb_strtoupper(mb_substr($value,0,1)) . mb_substr($value,1);
    }
}
