<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Hreflang_Category extends MageWorx_SeoBase_Model_Hreflang_Abstract
{
    /**
     * @return array
     */
    protected function _getHreflangUrls() {

        $hreflangUrls  = array();

        $currentUrl       = Mage::helper('core/url')->getCurrentUrl();
        $isFiltersApplyed = Mage::helper('mageworx_seoall/layeredFilter')->isApplyedLayeredNavigationFilters();

        if (strpos($currentUrl, '?') === false && !$isFiltersApplyed) {

            $category = Mage::getSingleton('catalog/layer')->getCurrentCategory();

            if (!$category) {
                return $hreflangUrls;
            }

            if (!$category->getId()) {
                return $hreflangUrls;
            }

            $hreflangRawCodes = Mage::helper('mageworx_seobase/hreflang')->getAlternateFinalCodes('category');
            if (empty($hreflangRawCodes)) {
                return $hreflangUrls;
            }

            $attributeCodes = array('is_active');
            $attributes = $this->_getAttributes($category, $attributeCodes, $hreflangRawCodes);

            $hreflangCodes = array();

            foreach ($hreflangRawCodes as $storeId => $storeCode) {

                $attributeStoreData = $this->_getAttributeStoreData($attributes, $storeId);

                if (!empty($attributeStoreData['is_active']) && $attributeStoreData['is_active'] === '1') {
                    if ($this->_issetCrossDomainStore($storeId)) {
                        if (Mage::app()->getStore()->getStoreId() == $storeId) {
                            $hreflangUrls = array();
                            break;
                        } else {
                            continue;
                        }
                    }
                    $hreflangCodes[$storeId] = $storeCode;
                }
            }

            $hreflangResource = Mage::helper('mageworx_seobase/factory')->getCategoryAlternateUrlResource();
            $hreflangUrlsCollection = $hreflangResource->getAllCategoryUrls(array_keys($hreflangCodes), $category->getId());

            if (empty($hreflangUrlsCollection[$category->getId()]['alternateUrls'])) {
                return $hreflangUrls;
            }

            foreach ($hreflangUrlsCollection[$category->getId()]['alternateUrls'] as $store => $altUrl) {
                $hreflang = $hreflangCodes[$store];
                $hreflangUrls[$hreflang] = $this->_helperTrailingSlash->trailingSlash('category', $altUrl, $store);
            }
        }

        return $hreflangUrls;
    }

    /**
     * @param Magento_Catalog_Model_Category $product
     * @param array $attributeCodes
     * @param array $hreflangCodes
     * @return array
     */
    protected function _getAttributes($product, $attributeCodes, $hreflangCodes)
    {
        $resourceModel  = Mage::getResourceModel('mageworx_seobase/category');

        $attributes = $resourceModel->getAttributeRawValueByStores(
            $product->getId(),
            $attributeCodes,
            array_keys($hreflangCodes)
        );

        return $attributes;
    }

    /**
     * @param array $attributes
     * @param int $storeId
     * @return array
     */
    protected function _getAttributeStoreData(array $attributes, $storeId)
    {
        $defaultStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        $data = array();

        foreach ($attributes as $attributeCode => $attributeData) {

            if (isset($attributeData[$storeId])) {
                $data[$attributeCode] = $attributeData[$storeId];
            } elseif (isset($attributeData[$defaultStoreId])) {
                $data[$attributeCode] = $attributeData[$defaultStoreId];
            } else {
                $data[$attributeCode] = null;
            }
        }

        return $data;
    }


}

