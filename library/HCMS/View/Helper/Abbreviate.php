<?php

/**
 * Abbreviate view helper
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author boris
 *
 */
class HCMS_View_Helper_Abbreviate extends Zend_View_Helper_Abstract {

    /**
     *
     * @param string $input
     * @param integer $width
     * @return string 
     */

    public function abbreviate($input, $length, $ellipses = true, $strip_html = true) {
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

}
