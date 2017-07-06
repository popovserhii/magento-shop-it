<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Block_Catalog_Products extends Mage_Core_Block_Template
{
    public function getCollection()
    {
        $model      = Mage::getResourceModel('xsitemap/catalog_product_html');
        $collection = $model->getCollection($this->getCategory()->getId());

        return $collection;
    }

    public function getItemUrl($product)
    {
        $url = $product->getUrl();

        if (strpos($url, 'http') === false) {
            $url = Mage::getBaseUrl() . $url;
        }

        return Mage::helper('mageworx_seoall/trailingSlash')->trailingSlash('product', $url);
    }
}