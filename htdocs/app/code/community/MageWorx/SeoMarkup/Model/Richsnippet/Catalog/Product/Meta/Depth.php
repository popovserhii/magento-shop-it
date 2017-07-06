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
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Meta_Depth extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $depthValue = Mage::helper('mageworx_seomarkup')->getDepthValue($this->_product);
        if ($depthValue) {
            $node->innertext = $node->innertext . '<meta itemprop="depth" content="'. $depthValue. '">' . "\n";
            return true;
        }
        
        return false;
    }

    protected function _getItemConditions()
    {
        return array("*[itemtype=http://schema.org/Product]");
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