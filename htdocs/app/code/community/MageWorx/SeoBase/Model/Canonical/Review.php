<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Canonical_Review extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    /**
     *
     * @var string
     */
    protected $_entityType = 'review';

    /**
     *
     * @param Mage_Catalog_Model_Category|null $item
     * @param int|null $storeId
     * @return string
     */
    protected function _getCanonicalUrl($item = null, $storeId = null)
    {
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $url        = $currentUrl;

        $toolbar = Mage::app()->getLayout()->getBlock('product_review_list.toolbar');

        if (is_object($toolbar) && ($toolbar instanceof Mage_Catalog_Block_Product_List_Toolbar)) {
            $availableLimit = $toolbar->getAvailableLimit();
        }
        else {
            $availableLimit = false;
        }

        if (is_array($availableLimit) && !empty($availableLimit['all'])) {
            $url = $this->_addLimitAllToUrl($url, $toolbar);
        }
        else {
            $url = $this->_deleteSortParameters($url, $toolbar);
        }

        $canonicalUrl = (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) ? $url : $currentUrl;

        return $this->renderUrl($canonicalUrl);
    }
}