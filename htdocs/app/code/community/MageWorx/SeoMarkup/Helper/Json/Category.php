<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Category extends Mage_Core_Helper_Abstract
{

    public function getJsonCategoryData($productCollection)
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isCategoryRichsnippetEnabled()) {
            return false;
        }

        $category = Mage::registry('current_category');
        if (!is_object($category)) {
            return false;
        }

        $data = array();
        $data['@context']    = 'http://schema.org';
        $data['@type']       = 'WebPage';
        $data['url']         = Mage::helper('core/url')->getCurrentUrl();
        $data['mainEntity']                    = array();
        $data['mainEntity']['@context']        = 'http://schema.org';
        $data['mainEntity']['@type']           = 'ItemList';
        $data['mainEntity']['name']            = $category->getName();
        $data['mainEntity']['url']             = Mage::helper('core/url')->getCurrentUrl();
        $data['mainEntity']['numberOfItems']   = count($productCollection->getItems());
        $data['mainEntity']['itemListElement'] = array();

        foreach ($productCollection as $product) {
            $data['mainEntity']['itemListElement'][] = $this->_getProductData($product);
        }

        return $data;
    }

    protected function _getProductData($product)
    {
        $data = array();
        $data['@type']    = "Product";
        $data['url']      = Mage::helper('mageworx_seomarkup')->getStoreBaseUrl() . $product->getRequestPath();
        $data['name']     = $product->getName();
        $data['image']    = $product->getImageUrl();

        if (Mage::helper('mageworx_seomarkup/config')->isUseOfferForCategoryProducts()) {
            $offerData        = $this->_getOfferData($product);
            if (!empty($offerData['price'])) {
                $data['offers'] = $offerData;
            }
        }

        return $data;
    }

    protected function _getOfferData($product)
    {
        $data   = array();
        $prices = Mage::helper('mageworx_seomarkup/price')->getPricesByProductType($product->getTypeId(), $product);

        if (is_array($prices) && count($prices)) {
            $data['price'] = $prices[0];
        }

        $data['priceCurrency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        $availability = Mage::helper('mageworx_seomarkup')->getAvailability($product);
        if ($availability) {
            $data['availability'] = $availability;
        }

        $condition = Mage::helper('mageworx_seomarkup')->getConditionValue($product);
        if ($condition) {
            $data['itemCondition'] = $condition;
        }

        return $data;
    }
}