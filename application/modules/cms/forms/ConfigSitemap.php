<?php
/**
 * Cms Sitemap add/edit form
 *
 * @package Modules
 * @subpackage Cms
 * @copyright Horisen
 * @author marko
 */
class Cms_Form_ConfigSitemap extends HCMS_Form_Simple
{
    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data = null, array $options = null) {
        $filterRules = array();
        $validatorRules = array(
            'menu'  => array(
                'allowEmpty'    => true
            )
        );
        parent::__construct($filterRules,$validatorRules, $data, $options);
    }
}