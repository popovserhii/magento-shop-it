<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Product extends Mage_Core_Helper_Abstract
{
    protected $_product;

    public function getJsonProductData($product)
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isProductRichsnippetEnabled()) {
            return false;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isProductJsonLdMethod()) {
            return false;
        }

        $this->_product = $product;
        $data = array();
        $data['@context']    = 'http://schema.org';
        $data['@type']       = 'Product';
        $data['name']        = $product->getName();
        $data['description'] = Mage::helper('mageworx_seomarkup')->getDescriptionValue($this->_product);
        $data['image']       = Mage::helper('mageworx_seomarkup')->getProductImage($this->_product);
        $data['offers']      = $this->_getOfferData();

        if (!$data['offers']['price']) {
            return false;
        }

        if (Mage::helper('mageworx_seomarkup/config')->isRatingEnabled()) {
            $aggregateRatingData = Mage::helper('mageworx_seomarkup')->getAggregateRatingData($this->_product, false);

            if (Mage::helper('mageworx_seomarkup')->isValidReviewData($aggregateRatingData)) {
                $data['aggregateRating'] = $aggregateRatingData;
            }
        }

        $color = Mage::helper('mageworx_seomarkup')->getColorValue($this->_product);
        if ($color) {
            $data['color'] = $color;
        }

        $brand = Mage::helper('mageworx_seomarkup')->getBrandValue($this->_product);
        if ($brand) {
            $data['brand'] = $brand;
        }

        $manufacturer = Mage::helper('mageworx_seomarkup')->getManufacturerValue($this->_product);
        if ($manufacturer) {
            $data['manufacturer'] = $manufacturer;
        }

        $model = Mage::helper('mageworx_seomarkup')->getModelValue($this->_product);
        if ($model) {
            $data['model'] = $model;
        }

        $gtin =  Mage::helper('mageworx_seomarkup')->getGtinData($this->_product);
        if (!empty($gtin['gtinType']) && !empty($gtin['gtinValue'])) {
            $data[$gtin['gtinType']] = $gtin['gtinValue'];
        }

        $skuValue = Mage::helper('mageworx_seomarkup')->getSkuValue($this->_product);
        if ($skuValue) {
            $data['sku'] = $skuValue;
        }

        $heightValue = Mage::helper('mageworx_seomarkup')->getHeightValue($this->_product);
        if ($heightValue) {
            $data['height'] = $heightValue;
        }

        $widthValue = Mage::helper('mageworx_seomarkup')->getWidthValue($this->_product);
        if ($widthValue) {
            $data['width'] = $widthValue;
        }

        $depthValue = Mage::helper('mageworx_seomarkup')->getDepthValue($this->_product);
        if ($depthValue) {
            $data['depth'] = $depthValue;
        }

        $weightValue = Mage::helper('mageworx_seomarkup')->getWeightValue($this->_product);
        if ($weightValue) {
            $data['weight'] = $weightValue;
        }

        $categoryName = Mage::helper('mageworx_seomarkup')->getCategoryValue($this->_product);
        if ($categoryName) {
            $data['category'] = $categoryName;
        }

        $customProperties = Mage::helper('mageworx_seomarkup/config')->getCustomProperties();
        if ($customProperties) {
            foreach ($customProperties as $propertyName => $propertyValue) {
                if ($propertyName && $propertyValue) {
                    $value = Mage::helper('mageworx_seomarkup')->getCustomPropertyValue($product, $propertyValue);
                    if ($value) {
                        $data[$propertyName] = $value;
                    }
                }
            }
        }
        return $data;
    }

    protected function _getOfferData()
    {
        $data   = array();
        $data['@type'] = MageWorx_SeoMarkup_Helper_Data::OFFER;

        $prices = Mage::helper('mageworx_seomarkup/price')->getPricesByProductType($this->_product->getTypeId());
        if (is_array($prices) && count($prices)) {
            $data['price'] = $prices[0];
        }

        $data['priceCurrency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        $availability = Mage::helper('mageworx_seomarkup')->getAvailability($this->_product);
        if ($availability) {
            $data['availability'] = $availability;
        }

        $condition = Mage::helper('mageworx_seomarkup')->getConditionValue($this->_product);
        if ($condition) {
            $data['itemCondition'] = $condition;
        }

        $paymentMethods = Mage::helper('mageworx_seomarkup')->getPaymentMethods();
        if ($paymentMethods) {
            $data['acceptedPaymentMethod'] = $paymentMethods;
        }

        $deliveryMethods = Mage::helper('mageworx_seomarkup')->getDeliveryMethods();
        if ($deliveryMethods) {
            $data['availableDeliveryMethod'] = $deliveryMethods;
        }

        return $data;
    }
}