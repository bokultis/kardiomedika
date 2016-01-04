<?php
/**
 * Simple form, extented from Zend_Filter_Input
 *
 * @package HCMS
 * @subpackage Form
 * @copyright Horisen
 * @author milan
 */
class HCMS_Form_Simple extends Zend_Filter_Input
{
    /**
     * Get unescaped valid form values
     * 
     * @return mixed
     */
    public function getValues(){
        return $this->getUnescaped();
    }

    /**
     *
     * Comment double quotes
     *
     * @return string
     */
    protected function _getNotEmptyMessage($rule, $field)
    {
        $message = $this->_defaults[self::NOT_EMPTY_MESSAGE];

//        if (null !== ($translator = $this->getTranslator())) {
//            if ($translator->isTranslated(self::NOT_EMPTY_MESSAGE)) {
//                $message = $translator->translate(self::NOT_EMPTY_MESSAGE);
//            } else {
//                $message = $translator->translate($message);
//            }
//        }

        $message = str_replace('%rule%', $rule, $message);
        $message = str_replace('%field%', $field, $message);
        return $message;
    }
}
