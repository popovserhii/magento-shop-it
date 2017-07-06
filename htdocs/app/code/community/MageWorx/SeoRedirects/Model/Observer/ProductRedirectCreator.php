<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Observer_ProductRedirectCreator extends Mage_Core_Model_Abstract
{
    protected $_priorityCategories = array();

    protected $_productNames = array();

    public function createRedirect($observer)
    {
        $product = $observer->getEvent()->getProduct();

        if (!$product) {
            return;
        }

        //duplicated and non-saved product
        if (!$product->getSku()) {
            return;
        }

        $collection = Mage::getResourceModel('core/url_rewrite_collection')
            ->filterAllByProductId($product->getId());

        $collection->getSelect()->order('store_id');

        if (!$collection->count()) {
            return;
        }

        $productInfo['product_id'] = $product->getId();
        $productInfo['name'] = $product->getName();
        $productInfo['sku']  = $product->getSku();

        foreach ($collection as $rewrite) {

            $priorityCategoryId = $this->_getPriorityCategoryByProduct($product, $rewrite['store_id']);

            if (!$priorityCategoryId) {
                continue;
            }

            $redirect = Mage::getModel('mageworx_seoredirects/redirect_product');

            $redirect->setRequestPath($rewrite['request_path']);
            $redirect->setStoreId($rewrite['store_id']);
            $redirect->setProductId($product->getId());

            if (empty($rewrite['category_id'])) {
                $redirect->setCategoryId($priorityCategoryId);
            } else {
                $redirect->setCategoryId($rewrite['category_id']);
            }
            $redirect->setPriorityCategoryId($priorityCategoryId);
            $redirect->setProductId($product->getId());
            $redirect->setProductSku($product->getSku());
            $redirect->setProductName($this->_getProductName($product->getId(), $rewrite['store_id']));
            $redirect->setDateCreated(Mage::getSingleton('core/date')->gmtDate());
            $redirect->save();

            if (strpos($rewrite['target_path'], 'catalog/product/view/') === 0) {
                $redirect->setRedirectId(null);
                $redirect->setRequestPath($rewrite['target_path']);
                $redirect->save();
            }
        }

        return $this;
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    protected function _getPriorityCategoryByProduct($product, $storeId)
    {
        if (!isset($this->_priorityCategories[$storeId])) {

            $categoryIds = $product->getCategoryIds();

            if (!$categoryIds) {
                $this->_priorityCategories[$storeId] = false;
                return $this->_priorityCategories[$storeId];
            }

            $categoryPriority = array();

            $rootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();
            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->addAttributeToFilter('entity_id', $categoryIds);
            $collection->addFieldToFilter('is_active', array('eq' => 1));
            $collection->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"));
            $collection->addAttributeToSelect('redirect_priority');
            $collection->setStoreId($storeId);

            foreach ($collection->getItems() as $category) {
                $redirectPriority = empty($category['redirect_priority']) ? 0 : (int)$category['redirect_priority'];
                $categoryPriority[$category->getId()] = $redirectPriority;
            }

            reset($categoryPriority);
            arsort($categoryPriority);

            if ($categoryPriority) {
                $this->_priorityCategories[$storeId] = key($categoryPriority);
            } else {
                $this->_priorityCategories[$storeId] = false;
            }
        }

        return $this->_priorityCategories[$storeId];
    }

    /**
     *
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    protected function _getProductName($productId, $storeId)
    {
        if (!array_key_exists($storeId, $this->_productNames)) {
            $this->_productNames[$storeId] =
                Mage::getResourceModel('catalog/product')->getAttributeRawValue($productId, 'name', $storeId);
        }

        return $this->_productNames[$storeId];
    }
}