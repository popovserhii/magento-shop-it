<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Hreflang_Product extends MageWorx_SeoBase_Model_Hreflang_Abstract
{
    protected $_currentAttributeValues = array();

    /**
     * @return array
     */
    protected function _getHreflangUrls()
    {
        $hreflangUrls = array();
        $product      = Mage::registry('current_product');

        if (!is_object($product)) {
            return $hreflangUrls;
        }

        if (!$product->getId()) {
            return $hreflangUrls;
        }

        $hreflangCodes   = Mage::helper('mageworx_seobase/hreflang')->getAlternateFinalCodes('product');
        $hreflangCodes   = array_intersect_key($hreflangCodes, array_flip($product->getStoreIds()));

        if (empty($hreflangCodes)) {
            return $hreflangUrls;
        }

        $attributeCodes  = array('visibility',  'status', 'canonical_url', 'canonical_cross_domain');

        $this->_fillCurrentAttributeValues($product, $attributeCodes);
        $attributes = $this->_getAttributes($product, $attributeCodes, $hreflangCodes);

        foreach ($hreflangCodes as $storeId => $storeCode) {

            $product->setStoreId($storeId);
            $this->_setAttributeValueToProduct($product, $attributes, $storeId);

            if ($this->_isProductDisabled($product)) {
                continue;
            }

            if (!$product->isVisibleInSiteVisibility()) {
                continue;
            }

            if ($this->_issetCrossDomainStore($product, $hreflangCodes) || $this->_isCustomCanonical($product)) {
                if (Mage::app()->getStore()->getStoreId() == $storeId) {
                    $hreflangUrls = array();
                    break;
                } else {
                    continue;
                }
            }

            $hreflangUrls[$storeCode] = Mage::getSingleton('mageworx_seobase/canonical_product')->getCanonicalUrl($product, $storeId);
        }

        $this->_restoreAttributeValues($product);

        return array_filter($hreflangUrls);
    }

    /**
     * @param Magento_Catalog_Model_Product $product
     * @param array $attributeCodes
     * @param int $storeId
     * @return void
     */
    protected function _setAttributeValueToProduct($product, array $attributes, $storeId)
    {
        $defaultStoreId  = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;

        foreach ($attributes as $attributeCode => $attributeData) {
            if (isset($attributeData[$storeId])) {
                $product->setData($attributeCode, $attributeData[$storeId]);
            } elseif (isset($attributeData[$defaultStoreId])) {
                $product->setData($attributeCode, $attributeData[$defaultStoreId]);
            } else {
                $product->setData($attributeCode, null);
            }
        }
    }

    /**
     * @param Magento_Catalog_Model_Product $product
     * @return boolean
     */
    protected function _issetCrossDomainStore($product)
    {
        $crossDomainStoreId = $product->getCanonicalCrossDomain();

        if ($crossDomainStoreId) {
            return true;
        }

        return parent::_issetCrossDomainStore($product->getStoreId());
    }

    /**
     * @param Magento_Catalog_Model_Product $product
     * @return bool
     */
    protected function _isCustomCanonical($product)
    {
        return (boolean)Mage::helper('mageworx_seobase/canonical')->getPersonalCanonicalUrlCode($product);
    }

    /**
     * @param Magento_Catalog_Model_Product $product
     * @param array $attributeCodes
     * @param array $hreflangCodes
     * @return array
     */
    protected function _getAttributes($product, $attributeCodes, $hreflangCodes)
    {
        $resourceModel  = Mage::getResourceModel('mageworx_seobase/product');

        $attributes = $resourceModel->getAttributeRawValueByStores(
            $product->getId(),
            $attributeCodes,
            array_keys($hreflangCodes)
        );

        return $attributes;
    }

    /**
     * @param Magento_Catalog_Model_Product $product
     * @param array $attributeCodes
     * @return void
     */
    protected function _fillCurrentAttributeValues($product, $attributeCodes)
    {
        $attributeCodes[] = 'do_not_use_category_id';

        foreach ($attributeCodes as $attributeCode) {
            $this->_currentAttributeValues[$attributeCode] = $product->getData($attributeCode);
        }

        $this->_currentAttributeValues[$attributeCode] = $product->getDoNotUseCategoryId();
    }

    /**
     * @param Magento_Catalog_Model_Product $product
     * @return void
     */
    protected function _restoreAttributeValues($product)
    {
        foreach ($this->_currentAttributeValues as $attributeCode => $attributeValue) {
            $product->setData($attributeCode, $attributeValue);
        }

        $product->setData('store_id', Mage::app()->getStore()->getStoreId());
    }

    /**
     * Checks whether product has disabled status
     * Magento 1.6.x workaround
     *
     * @return bool
     */
    protected function _isProductDisabled($product)
    {
        if (method_exists($product, 'isDisabled')) {
            return $product->isDisabled();
        }

        return $product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED;
    }

}