<?php

/**
 * Description of Phonenumber
 *
 * @author marko
 */
class HCMS_Filter_Phonenumber implements Zend_Filter_Interface
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
        return Horisen_Utils_Phone::stripPhone($value);
    }
}
