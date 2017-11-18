<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Block_Container extends Mage_Core_Block_Template
{
    const XML_PATH_SHOW_STORES     = 'mageworx_seo/xsitemap/show_stores';
    const XML_PATH_SHOW_CATEGORIES = 'mageworx_seo/xsitemap/show_categories';
    const XML_PATH_SHOW_PAGES      = 'mageworx_seo/xsitemap/show_pages';
    const XML_PATH_SHOW_LINKS      = 'mageworx_seo/xsitemap/show_links';

    protected function _construct()
    {
        $this->setTitle($this->__('Site Map'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->getTitle());
    }

    public function showStores()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_STORES);
    }

    public function showCategories()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_CATEGORIES);
    }

    public function showPages()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PAGES);
    }

    public function showLinks()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SHOW_LINKS);
    }

}
