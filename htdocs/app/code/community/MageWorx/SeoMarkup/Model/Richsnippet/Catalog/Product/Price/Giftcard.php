<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Price_Giftcard extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Price_Abstract
{
    protected $_validBlockType = 'Mage_Catalog_Block_Product_View_Abstract';

    protected function _getItemValues($_product = null)
    {
        if (!$_product) {
            $_product = $this->_product;
        }        
        $prices = Mage::helper('mageworx_seomarkup/price')->getGiftcardPrices($_product);
        
        $modPrices = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $modPrices = array_merge($modPrices, $this->_getModifyPrices($price));
            }
        }
        return array_unique($modPrices);
    }
}
