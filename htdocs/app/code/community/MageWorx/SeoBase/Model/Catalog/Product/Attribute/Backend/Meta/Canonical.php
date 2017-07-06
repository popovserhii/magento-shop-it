<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_Catalog_Product_Attribute_Backend_Meta_Canonical extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    private $_hashId;

    public function beforeSave($object)
    {
        if (!Mage::app()->getRequest()->getParam('canonical_url_custom')) {
            return parent::beforeSave($object);
        }
        $stores = array();
        if (Mage::app()->getRequest()->getParam('store')) {
            $stores[] = Mage::app()->getRequest()->getParam('store');
            $product  = Mage::getSingleton('catalog/product')
                    ->setStoreId(Mage::app()->getRequest()->getParam('store'))
                    ->load($object->getId());
        }
        else {
            foreach (Mage::app()->getStores() as $store) {
                $product = Mage::getSingleton('catalog/product')
                        ->setStoreId($store->getId())
                        ->load($object->getId());
                if ($product) {
                    $stores[] = $store->getId();
                }
            }
            $product = Mage::getSingleton('catalog/product')->load($object->getId());
        }

        $hashID = str_replace('0.', '', str_replace(' ', '_', microtime()));
        foreach ($stores as $storeId) {
            if (!$this->_hashId) {
                $this->_hashId = $hashID;
            }

            try {
                Mage::getModel('core/url_rewrite')
                        ->setStoreId($storeId)
                        ->setCategoryId(null)
                        ->setProductId($object->getId())
                        ->setIdPath($hashID)
                        ->setRequestPath(Mage::app()->getRequest()->getParam('canonical_url_custom'))
                        ->setTargetPath($product->getUrlPath())
                        ->setIsSystem(0)
                        ->setOptions('RP')
                        ->save();
            }
            catch (Exception $e) {
                $obj           = Mage::getModel('core/url_rewrite')->load(Mage::app()->getRequest()->getParam('canonical_url_custom'),
                        'request_path');
                $this->_hashId = $obj->getIdPath();
            }
        }
        $product                  = Mage::app()->getRequest()->getParam('product');
        $product['canonical_url'] = $this->_hashId;
        Mage::app()->getRequest()->setParam('product', $product);
        $object->setCanonicalUrl($this->_hashId);
        return parent::beforeSave($object);
    }

}