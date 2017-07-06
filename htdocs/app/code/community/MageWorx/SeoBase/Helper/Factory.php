<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Helper_Factory extends Mage_Core_Helper_Abstract
{
    public function getProductAlternateUrlResource()
    {
        if(Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            return Mage::getResourceModel('mageworx_seobase/hreflang_catalog_ee_product');
        }
        return Mage::getResourceModel('mageworx_seobase/hreflang_catalog_ce_product');
    }

    public function getCategoryAlternateUrlResource()
    {
        if(Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            return Mage::getResourceModel('mageworx_seobase/hreflang_catalog_ee_category');
        }
        return Mage::getResourceModel('mageworx_seobase/hreflang_catalog_ce_category');
    }
}