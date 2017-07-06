<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_Catalog_Product_Attribute_Source_Meta_Canonical extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        $product = Mage::registry('current_product');

        if (!is_object($product)) {
            return array();
        }

        $canonicalUrl = $product->getCanonicalUrl();

        if (!$this->_options) {
            $this->_options = array(
                array('value' => '', 'label' => Mage::helper('mageworx_seobase')->__('Use Config')),
                array('value' => 'custom', 'label' => Mage::helper('mageworx_seobase')->__('Use Custom')),
            );

            $storeId = (int) Mage::app()->getRequest()->getParam('store', 0);

            if (!$storeId && Mage::app()->isSingleStoreMode()) {
                $storeId = Mage::app()->getStore(true)->getId();
            }

            if ($product->getId()) {
                $collection = Mage::getResourceModel('mageworx_seobase/core_url_rewrite_collection')
                        ->filterAllByProductId($product->getId())
                        ->groupByUrl()
                        ->sortByLength('ASC');
                if ($storeId > 0) {
                    $collection->addStoreFilter($storeId, false);
                }

                $exists = false;
                if ($collection->count()) {
                    foreach ($collection->getItems() as $urlRewrite) {
                        if ($urlRewrite->getIdPath() == $canonicalUrl) {
                            $exists = true;
                        }

                        if (!isset($this->_options[$urlRewrite->getStoreId() + 2])) {
                            $this->_options[$urlRewrite->getStoreId() + 2] = array(
                                'label' => Mage::app()->getStore($urlRewrite->getStoreId())->getName(),
                                'value' => array()
                            );
                        }
                        $this->_options[$urlRewrite->getStoreId() + 2]['value'][] = array(
                            'value' => $urlRewrite->getIdPath(),
                            'label' => $urlRewrite->getRequestPath()
                        );

                    }
                }
                if (!$exists) {
                    $urlRewriteModel = Mage::getModel('core/url_rewrite')->load($canonicalUrl, 'id_path');
                    if ($urlRewriteModel->getId()) {
                        $this->_options[$urlRewriteModel->getStoreId() + 2]['label'] = '';
                        $this->_options[$urlRewriteModel->getStoreId() + 2]['value'][] = array(
                            'value' => $urlRewriteModel->getIdPath(),
                            'label' => $urlRewriteModel->getRequestPath()
                        );
                    }
                }
            }
        }
        return $this->_options;
    }
}