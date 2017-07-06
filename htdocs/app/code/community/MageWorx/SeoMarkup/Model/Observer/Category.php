<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Model_Observer_Category
{
    public function createRichsnippetCategoryMarkup($observer)
    {
        if (!Mage::helper('mageworx_seomarkup')->isCategoryPage()) {
            return;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isCategoryRichsnippetEnabled()) {
            return false;
        }

        if (Mage::app()->getRequest()->isXmlHttpRequest()) {
            return false;
        }

        $block = $observer->getBlock();
        if ($block->getNameInLayout() != 'root') {
            return false;
        }

        if (Mage::helper('mageworx_seomarkup/config')->isUseCategoryRobotsRestriction() && $this->_isNoindexPage()) {
            return false;
        }

        $currentCategory = Mage::registry('current_category');
        if ($currentCategory->getDisplayMode() == 'PAGE') {
           return false;
        }

        $productCollection = $this->_getProductCollection();
        if (empty($productCollection)) {
            return false;
        }

        $jsonCategoryHelper = Mage::helper('mageworx_seomarkup/json_category');
        $categoryRichsnippetData = $jsonCategoryHelper->getJsonCategoryData($productCollection);

        if (!empty($categoryRichsnippetData)) {
            $transport    = $observer->getTransport();
            $normalOutput = $observer->getTransport()->getHtml();
            $catJson = '<script type="application/ld+json">' . json_encode($categoryRichsnippetData) . '</script>';
            $modifyOutput = str_replace('</head>', "\n" . $catJson . '</head>', $normalOutput);
            $transport->setHtml($modifyOutput);
        }

        return $this;
    }

    protected function _getProductCollection()
    {
        $productList = Mage::app()->getLayout()->getBlock('product_list');
        if (is_object($productList) && ($productList instanceof Mage_Catalog_Block_Product_List)) {
            return $productList->getLoadedProductCollection();
        }

        $pager = Mage::app()->getLayout()->getBlock('product_list_toolbar_pager');
        if (!is_object($pager)) {
            $pager = $this->_getPagerFromToolbar();
        } elseif (!$pager->getCollection()) {
            $pager = $this->_getPagerFromToolbar();
        }

        if(!is_object($pager)){
            return false;
        }

        return $pager->getCollection();
    }

    protected function _getPagerFromToolbar()
    {
        $toolbar = Mage::app()->getLayout()->getBlock('product_list_toolbar');
        if (is_object($toolbar)) {
            $pager = $toolbar->getChild('product_list_toolbar_pager');
        }
        return is_object($pager) ? $pager : false;
    }

    protected function _isNoindexPage()
    {
        $head = Mage::app()->getLayout()->getBlock('head');
        if (is_object($head) && ($head instanceof Mage_Page_Block_Html_Head)) {
            $robots = $head->getRobots();
            if ($robots && stripos($robots, 'noindex') !== false) {
                return true;
            }
        }

        return false;
    }
}