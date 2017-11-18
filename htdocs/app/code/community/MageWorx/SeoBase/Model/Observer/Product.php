<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_Product extends Mage_Core_Model_Abstract
{
    /**
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function prepareCanonicalUrl($observer)
    {
    	$storeId = $this->_getStore();

        //catalog_product_save_before
        if (!$storeId) {
            return;
        }

        $product = $observer->getData('product');

        if (!is_object($product)) {
            return;
        }

        if (!Mage::app()->getRequest()->getParam('canonical_url_custom')) {
            $this->_deleteOldCustomRewriteIfExists($product);
            return;
        }


        $hashId = str_replace('0.', '', str_replace(' ', '_', microtime()));

        try {
            $rewrite = Mage::getModel('core/url_rewrite');
            $rewrite->setStoreId($storeId)
                    ->setCategoryId(null)
                    ->setProductId($product->getId())
                    ->setIdPath($hashId)
                    ->setRequestPath(Mage::app()->getRequest()->getParam('canonical_url_custom'))
                    ->setTargetPath($product->getUrlPath())
                    ->setIsSystem(0)
                    ->setOptions('RP')
                    ->save();
        }
        catch (Exception $e) {
           ///If "Request Path for Specified Store already exists"

            $obj = Mage::getModel('core/url_rewrite')->load(Mage::app()->getRequest()->getParam('canonical_url_custom'),
            'request_path');
            $hashId = $obj->getIdPath();
        }

        $product->setCanonicalUrl($hashId);
        $this->_deleteOldCustomRewriteIfExists($product);
    }

    protected function _getStore()
    {
    	if(Mage::app()->isSingleStoreMode()){
    		return Mage::app()->getStore(true)->getId();
    	}
        return Mage::app()->getRequest()->getParam('store');
    }

    /**
     * Clear unused URL rewrites
     *
     * @param Mage_Catalog_Model_Product $product
     * @return void
     */
    protected function _deleteOldCustomRewriteIfExists($product)
    {
        $oldCanonicalCode = $product->getOrigData('canonical_url');
        $newCanonicalCode = $product->getData('canonical_url');

        if ($oldCanonicalCode && strpos($oldCanonicalCode, '_') !== false && $oldCanonicalCode != $newCanonicalCode) {
            list($num1, $num2) = explode('_', $oldCanonicalCode);
            if (!empty($num1) && !empty($num2) && is_numeric($num1) && is_numeric($num2)) {
                $rewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($product->getOrigData('canonical_url'));
                if ($rewrite && $rewrite->getUrlRewriteId() &&
                   $rewrite->getStoreId() == $product->getStoreId() &&
                   $rewrite->getProductId() == $product->getId() ) {
                   $rewrite->delete();
                }
            }
        }
    }
}