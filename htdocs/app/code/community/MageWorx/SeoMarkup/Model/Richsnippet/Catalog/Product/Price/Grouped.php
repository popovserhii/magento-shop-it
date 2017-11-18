<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Price_Grouped extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Price_Abstract
{
    protected $_validViewBlockType = 'Mage_Catalog_Block_Product_View_Type_Grouped';

    /**
     * Return array prices (include tax / exclude tax sorted by priority)
     * of associated product have minimal price or itself prices.
     * @return array
     */
    protected function _getItemValues($product = null)
    {
        $associatedProducts = $this->_product->getTypeInstance(true)->getAssociatedProducts($this->_product);        
                
        if (count($associatedProducts)) {
            $allProductPrices = array();
            foreach ($associatedProducts as $product) {
                $productPrices = parent::_getItemValues($product);
                $allProductPrices[(string) $productPrices[0]] = $productPrices;
            }

            if (count($allProductPrices)) {
                ksort($allProductPrices);
                return array_shift($allProductPrices);
            }
        }
        return parent::_getItemValues();
    }
    
    protected function _checkBlockType()
    {
        return true;
    }
}