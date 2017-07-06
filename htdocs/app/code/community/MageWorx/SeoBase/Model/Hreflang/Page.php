<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Hreflang_Page extends MageWorx_SeoBase_Model_Hreflang_Abstract
{
    /**
     * @return array
     */
    protected function _getHreflangUrls()
    {
        $hreflangUrls  = array();

        $page = Mage::getSingleton('cms/page');

        if (is_object($page) && $page->getPageId() && !Mage::registry('current_cms_hierarchy_node')) {

            $itemId = $page->getPageId();

            $hreflangRawCodes = Mage::helper('mageworx_seobase/hreflang')->getAlternateFinalCodes('cms');
            if (empty($hreflangRawCodes)) {
                return null;
            }

            $hreflangCodes = array();

            foreach ($hreflangRawCodes as $storeId => $storeCode) {
                if ($this->_issetCrossDomainStore($storeId)) {
                    if (Mage::app()->getStore()->getStoreId() == $storeId) {
                        return $hreflangUrls = array();
                    } else {
                        continue;
                    }
                }
                $hreflangCodes[$storeId] = $storeCode;
            }

            $hreflangUrlsCollection = Mage::getResourceModel('mageworx_seobase/hreflang_cms_page')
                ->getAllCmsUrls(array_keys($hreflangCodes), Mage::app()->getStore()->getStoreId(), $itemId, false);

            if (empty($hreflangUrlsCollection[$itemId]['alternateUrls'])) {
                return null;
            }

            foreach ($hreflangUrlsCollection[$itemId]['alternateUrls'] as $store => $altUrl) {
                $hreflang = $hreflangCodes[$store];
                $hreflangUrls[$hreflang] = $this->_helperTrailingSlash->trailingSlash('page', $altUrl, $store);
            }
        }
        return (!empty($hreflangUrls)) ? $hreflangUrls : null;
    }
}