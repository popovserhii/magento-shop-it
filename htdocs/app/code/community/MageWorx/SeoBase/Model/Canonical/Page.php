<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_Canonical_Page extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    /**
     *
     * @var string
     */
    protected $_entityType = 'page';

    /**
     *
     * @param Mage_Catalog_Model_Category|null $item
     * @param int|null $storeId
     * @return string
     */
    protected function _getCanonicalUrl($item = null, $storeId = null)
    {
        if (!Mage::getSingleton('cms/page')) {
            return '';
        }

        if (!Mage::getSingleton('cms/page')->getPageId()) {
            return '';
        }

        $url = Mage::helper('cms/page')->getPageUrl(Mage::getSingleton('cms/page')->getPageId());
        return $this->renderUrl($url);
    }
}