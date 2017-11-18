<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Block_Catalog_Categories extends Mage_Core_Block_Template
{
    const XML_PATH_SHOW_PRODUCTS      = 'mageworx_seo/xsitemap/show_products';
    const XML_PATH_CATEGORY_MAX_LEVEL = 'mageworx_seo/xsitemap/category_max_level';

    protected $_storeRootCategoryPath  = '';
    protected $_storeRootCategoryLevel = 0;
    protected $_categories             = array();

    protected function _prepareLayout()
    {
        $parent                        = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())
            ->load(Mage::app()->getStore()->getRootCategoryId());
        $this->_storeRootCategoryPath  = $parent->getPath();
        $this->_storeRootCategoryLevel = $parent->getLevel();
        //$collection = $this->getTreeCollection();
        $this->getTreeCollection();
        //$this->setCollection($collection);
        return $this;
    }

    public function getCategories()
    {
        return $this->_categories;
    }

    public function getTreeCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*')
            ->joinUrlRewrite()
            ->addAttributeToFilter('is_active', 1)
            ->setOrder('level', 'ASC')
            ->setOrder(Mage::helper('xsitemap')->getHtmlSitemapSort());
        ;

        $categoryMaxLevel = $this->getCategoryMaxLevel();
        if($categoryMaxLevel){
            $collection->addAttributeToFilter('level', array('lteq' => $categoryMaxLevel));
        }

        // Magento v1.2.0.2 Compatibility
        $collection->getSelect()->where('e.path LIKE ?', $this->_storeRootCategoryPath . '/%');

        foreach ($collection->getItems() as $item) {

            if ($item->getData('exclude_from_html_sitemap')) {
                continue;
            }
            if (!isset($level)) {
                $level = $item->getLevel();
            }
            if ($item->getLevel() == $level) {
                $this->_categories[] = $item;
                ///if ($item->getChildrenCount()) {
                    $this->_addChildren($item->getId(), $collection);
                ///}
            }
        }
        return $collection;
    }

    protected function _addChildren($parentId, $collection)
    {
        foreach ($collection->getItems() as $item) {
            if ($item->getParentId() == $parentId) {
                if ($item->getData('exclude_from_html_sitemap')) {
                    continue;
                }
                $this->_categories[] = $item;
                ///if ($item->getChildrenCount()) {
                    $this->_addChildren($item->getId(), $collection);
                ///}
            }
        }
    }

    public function getLevel($item, $delta = 1)
    {
        return (int) ($item->getLevel() - $this->_storeRootCategoryLevel - 1) * $delta;
    }

    public function getCategoryMaxLevel($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_CATEGORY_MAX_LEVEL, $storeId);
    }

    public function getItemUrl($category)
    {
        $helper              = Mage::helper('catalog/category');
        $helperTrailingSlash = Mage::helper('mageworx_seoall/trailingSlash');
        /* @var $helper Mage_Catalog_Helper_Category */
        return $helperTrailingSlash->trailingSlash('category', $helper->getCategoryUrl($category));
    }

    /*
     * @param int It isn't remote because of compatibility with template
     */
    public function showProducts($category = false)
    {
        if (!isset($this->_showProducts)) {
            $this->_showProducts = Mage::getStoreConfigFlag(self::XML_PATH_SHOW_PRODUCTS);
        }
        return $this->_showProducts;
    }

}
