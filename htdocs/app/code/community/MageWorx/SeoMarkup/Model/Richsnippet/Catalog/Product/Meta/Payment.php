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
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Meta_Payment extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $string = $this->_getPaymentMethodsAsString();

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

    protected function _getPaymentMethodsAsString()
    {
        $paymentMethodList = Mage::helper('mageworx_seomarkup')->getPaymentMethods();

        if (count($paymentMethodList)) {
            $paymentMethodString = '';
            foreach ($paymentMethodList as $paymentMethod) {
                $paymentMethodString .= '<link itemprop="acceptedPaymentMethod" content="' . $paymentMethod . '"/>' . "\n";
            }
            return trim($paymentMethodString, "\n");
        }
        return null;
    }
}