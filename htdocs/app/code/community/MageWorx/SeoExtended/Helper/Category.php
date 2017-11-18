<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoExtended_Helper_Category extends Mage_Catalog_Helper_Category
{

    public function getCategoryUrlPath($urlPath, $slash = false, $storeId = null, $optimizedForceOff = false)
    {
        if(Mage::helper('seoextended/config')->isOptimizedUrlsEnabled() && !$optimizedForceOff){
            return '';
        }
        return parent::getCategoryUrlPath($urlPath, $slash, $storeId);
    }
}
