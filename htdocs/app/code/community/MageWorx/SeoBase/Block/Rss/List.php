<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Rss_List extends Mage_Rss_Block_List
{

    private function __addRssFeed($url, $label)
    {
        $this->_rssFeeds[] = new Varien_Object(
                array(
                    'url'   => $url,
                    'label' => $label
                )
        );
        return $this;
    }

    public function NewProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/new';
        if ((bool) Mage::getStoreConfig($path)) {
            $this->__addRssFeed(Mage::getUrl('rss/' . Mage::app()->getStore()->getCode() . '/@new'),
                    $this->__('New Products'));
        }
    }

    public function SpecialProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/special';
        if ((bool) Mage::getStoreConfig($path)) {
            $this->__addRssFeed(Mage::getUrl('rss/' . Mage::app()->getStore()->getCode() . '/@specials/' . $this->getCurrentCustomerGroupId()),
                    $this->__('Special/Discount Products'));
        }
    }

    public function SalesRuleProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/salesrule';
        if ((bool) Mage::getStoreConfig($path)) {
            $this->__addRssFeed(Mage::getUrl('rss/' . Mage::app()->getStore()->getCode() . '/@discounts/' . $this->getCurrentCustomerGroupId()),
                    $this->__('Coupons/Discounts'));
        }
    }

    public function CategoriesRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/category';
        if ((bool) Mage::getStoreConfig($path)) {
            $category = Mage::getModel('catalog/category');

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $treeModel = $category->getTreeModel()->loadNode(Mage::app()->getStore()->getRootCategoryId());
            $nodes     = $treeModel->loadChildren()->getChildren();

            $nodeIds = array();
            foreach ($nodes as $node) {
                $nodeIds[] = $node->getId();
            }

            $collection = $category->getCollection()
                    ->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_anchor')
                    ->addAttributeToFilter('is_active', 1)
                    ->addIdFilter($nodeIds)
                    ->addAttributeToSort('name')
                    ->load();

            foreach ($collection as $category) {
                $this->__addRssFeed(Mage::getUrl('rss/' . Mage::app()->getStore()->getCode() . '/' . $category->getUrlKey()),
                        $category->getName());
            }
        }
    }
}