<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Product extends Mage_Core_Model_Abstract
{
    protected $_variables = array('category', 'categories');

    protected $_variablesData = array();

    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param $observer
     * @return void
     */
    public function updateProductProperties($observer)
    {
        $product = $observer->getData('product');

        if (Mage::helper('mageworx_seoxtemplates/config')->isUseProductSeoName() && $product->getProductSeoName()) {
            $product->setName($product->getProductSeoName());
        }

        $metaTitleConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_product_metatitle');
        $metaTitle          = $metaTitleConverter->convert($product, $product->getMetaTitle(), true);
        $product->setMetaTitle($metaTitle);

        $metaDescriptionConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_product_metadescription');
        $metaDescription          = $metaDescriptionConverter->convert($product, $product->getMetaDescription(), true);
        $product->setMetaDescription($metaDescription);

        $metaKeywordsConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_product_metakeywords');
        $metaKeyword           = $metaKeywordsConverter->convert($product, $product->getMetaKeyword(), true);
        $product->setMetaKeyword($metaKeyword);

        $shortDescriptionConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_product_shortdescription');
        $shortDescription          = $shortDescriptionConverter->convert($product, $product->getShortDescription(), true);
        $product->setShortDescription($shortDescription);

        $descriptionConverter = Mage::getSingleton('mageworx_seoxtemplates/converter_product_description');
        $description          = $descriptionConverter->convert($product, $product->getDescription(), true);
        $product->setDescription($description);
    }
}
