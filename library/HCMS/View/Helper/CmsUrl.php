<?php
/**
 * CMS url view helper - prefixes theme path
 *
 * @package HCMS
 * @subpackage View
 * @copyright Horisen
 * @author milan
 *
 */
class HCMS_View_Helper_CmsUrl extends Zend_View_Helper_Abstract {

    /**
     * CMS url view helper - prefixes theme path
     *
     * @param int $pageId page id
     * @return string
     */
    public function cmsUrl($pageId, $lang = null, $reset = false) {
        return $this->view->url(array(
            'module'        => 'cms',
            'controller'    => 'page',
            'action'        => 'index',
            'page_id'       => $pageId,
            'lang'          => isset($lang)? $lang: CURR_LANG
        ), 'cms', $reset);
    }
}
