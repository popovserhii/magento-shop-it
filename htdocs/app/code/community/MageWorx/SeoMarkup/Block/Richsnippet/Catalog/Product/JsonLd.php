<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Block_Richsnippet_Catalog_Product_JsonLd extends Mage_Core_Block_Template
{
    protected function _getJsonData()
    {
        $product = Mage::registry('current_product');

        if (!is_object($product)) {
            return '';
        }

        $productJsonData = Mage::helper('mageworx_seomarkup/json_product')->getJsonProductData($product);
        
        if ($productJsonData) {
            return '<script type="application/ld+json">' . json_encode($productJsonData) . '</script>';
        }

        return '';
   }

   public function _toHtml()
   {
       return $this->_getJsonData();
   }

}