<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Canonical_Product extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    const LONGEST_BY_URL       = 1;
    const SHORTEST_BY_URL      = 2;
    const ROOT                 = 3;
    const LONGEST_BY_CATEGORY  = 4;
    const SHORTEST_BY_CATEGORY = 5;

    /**
     *
     * @var string
     */
    protected $_entityType = 'product';

    /**
     *
     * @param Mage_Catalog_Model_Product|null $item
     * @param int|null $storeId
     * @return string
     */
    protected function _getCanonicalUrl($item = null, $storeId = null)
    {
        $product = (is_object($item) && $item->getId()) ? $item : Mage::registry('current_product');
        if (!$product) {
            return '';
        }

        if (!$product->getIsMissAdditionalCanonicalMethods()) {

            $personalCanonicalUrlCode = Mage::helper('mageworx_seobase/canonical')->getPersonalCanonicalUrlCode($product);

            if ($personalCanonicalUrlCode) {
                $urlRewrite   = Mage::getModel('core/url_rewrite')->setStoreId($storeId)->loadByIdPath($personalCanonicalUrlCode);
                $personalCanonicalUrl = trim($urlRewrite->getRequestPath());

                if ($personalCanonicalUrl) {
                    if (strpos($personalCanonicalUrl, 'http') === 0) {
                        return $this->escapeUrl($personalCanonicalUrl);
                    }

                    return $this->_helperStore->getStoreBaseUrl($storeId) . ltrim($personalCanonicalUrl, '/');
                }
            }

            $personalCanonicalUrlPath = Mage::helper('mageworx_seobase/canonical')->getPersonalCanonicalUrlPath($product);

            if ($personalCanonicalUrlPath) {
                $urlRewrite   = Mage::getModel('core/url_rewrite')->setStoreId($storeId)->loadByIdPath($personalCanonicalUrlPath);
                $personalCanonicalUrl = $this->_helperStore->getStoreBaseUrl($storeId) . trim($urlRewrite->getRequestPath());
                return $this->renderUrl($personalCanonicalUrl);
            }

            if ($this->_helperData->isAssociatedCanonicalEnabled($storeId) &&
                $this->_helperData->isCompoundProductType($product->getTypeID()) === false
            ) {
                $compoundProduct = $this->_getLastCompoundProductByChildProductId($product->getId(), $storeId);

                if (is_object($compoundProduct) && $compoundProduct->getId()) {
                    return $this->_getCanonicalUrl($compoundProduct);
                }
            }

            $crossDomainStoreId = $this->_getCrossDomainStoreId($storeId, $product);

            if ($crossDomainStoreId) {
                $crossDomainCanonicalUrl = $this->_getUrlRewriteCanonical($product, $crossDomainStoreId);
                if ($crossDomainCanonicalUrl) {
                    return $this->renderUrl($crossDomainCanonicalUrl, $storeId);
                }
            }
        }

        $canonicalUrl = $this->_getUrlRewriteCanonical($product, $storeId);

        if (!$canonicalUrl || (strpos($canonicalUrl, '/product/view/id/') && !$product->getDoNotUseCategoryId()))
        {
            $product->setDoNotUseCategoryId(true);
            $canonicalUrl = $product->getProductUrl(false);
        }

        return $this->renderUrl($canonicalUrl);
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId
     * @return string
     */
    protected function _getUrlRewriteCanonical($product, $storeId)
    {
        if (!is_object($product)) {
            return '';
        }

        $canonicalUrl  = '';
        $canonicalType = $this->_helperData->getProductCanonicalType($storeId);

        if (Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {

            $canonicalUrl = Mage::getResourceModel('mageworx_seobase/core_url_rewrite_ee')
                ->getCanonicalUrl($product, $canonicalType, $storeId);
        }
        else {
            $collection = Mage::getResourceModel('mageworx_seobase/core_url_rewrite_collection')
                ->filterAllByProductId($product->getId())
                ->addStoreFilter($storeId, false)
                ->filterCanonicalUrl($canonicalType);

            $urlRewrite = $collection->getFirstItem();

            if ($urlRewrite && $urlRewrite->getRequestPath()) {
                $canonicalUrl = $this->_helperStore->getStoreBaseUrl($storeId) . $urlRewrite->getRequestPath();
            }
        }

        return $canonicalUrl;
    }

    /**
     *
     * @param int $productId
     * @param int $storeId
     * @return Mage_Catalog_Model_Product|null
     */
    protected function _getLastCompoundProductByChildProductId($productId, $storeId)
    {
        $ids          = $this->_getParentProductIds($productId);
        $productTypes = $this->_helperData->getProductTypeForReplaceCanonical($storeId);

        if (count($ids) && count($productTypes)) {
            $visibilityStatuses = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH
            );

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addIdFilter($ids)
                ->addStoreFilter($storeId)
                ->setStoreId($storeId)
                ->addAttributeToFilter('status', array('eq' => 1))
                ->addFieldToFilter('visibility', array('in' => $visibilityStatuses))
                ->addAttributeToFilter('type_id', array('in' => $productTypes))
                ->setOrder('entity_id', 'DESC');

            if ($collection->count()) {
                $product = $collection->getFirstItem();
                return $product;
            }
        }
        return null;
    }

    /**
     *
     * @param int $id
     * @return array
     */
    protected function _getParentProductIds($id)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn         = $coreResource->getConnection('core_read');
        $select       = $conn->select()
            ->from($coreResource->getTableName('catalog/product_relation'), array('parent_id'))
            ->where('child_id = ?', $id);

        return $conn->fetchCol($select);
    }
}