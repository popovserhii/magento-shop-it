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
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Meta_Category extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $categoryName = Mage::helper('mageworx_seomarkup')->getCategoryValue($this->_product);

        if (!empty($categoryName)) {
            $node->innertext = $node->innertext . '<meta itemprop="category" content="' . $categoryName . '">' . "\n";
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