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
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Meta_Delivery extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $string = $this->_getDeliveryMethodsAsString();

        if (!empty($string)) {
            $node->innertext = $node->innertext . $string . "\n";
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

    protected function _getDeliveryMethodsAsString()
    {
        $deliveryMethodList = Mage::helper('mageworx_seomarkup')->getDeliveryMethods();

        if (count($deliveryMethodList)) {
            $deliveryMethodString = '';
            foreach ($deliveryMethodList as $deliveryMethod) {
                $deliveryMethodString .= '<link itemprop="availableDeliveryMethod" content="' . $deliveryMethod . '"/>' . "\n";
            }
            return trim($deliveryMethodString, "\n");
        }
        return null;
    }

}