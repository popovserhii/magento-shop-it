<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


/**
 * @see MageWorx_SeoMarkup_Model_Catalog_Product_Richsnippet_Product
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Meta_Seller extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $sellerName    = trim(Mage::getStoreConfig('general/store_information/name'));
        $sellerPhone   = trim(Mage::getStoreConfig('general/store_information/phone'));
        $sellerCountry = trim(Mage::getStoreConfig('general/store_information/merchant_country'));
        $sellerAddress = trim(Mage::getStoreConfig('general/store_information/address'));

        $sellerInfo = '';
        $sellerInfo .= $sellerName ? 'Store name: ' . $sellerName : '';
        $sellerInfo .= $sellerPhone ? ', phone: ' . $sellerPhone  : '';
        $sellerInfo .= $sellerCountry ? ', country: ' . $sellerCountry  : '';
        $sellerInfo .= $sellerAddress ? ', address: ' . $sellerAddress  : '';

        $sellerInfo = trim($sellerInfo, ',');

        if ($sellerInfo) {
            $node->innertext = $node->innertext . '<meta itemprop="seller" content="' . $sellerInfo . '">' . "\n";
            return true;
        }
        return false;
    }

    protected function _getItemConditions()
    {
        return array("*[itemtype=" . MageWorx_SeoMarkup_Helper_Data::OFFER . "]");
    }

    protected function _checkBlockType()
    {
        return true;
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        return true;
    }

}