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
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Description extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _isValidNode(simple_html_dom_node $node)
    {
        $parentNode = $this->_findParentContainer($node);
        if (!$parentNode) {
            return false;
        }

        if ($parentNode->itemprop) {
            return false;
        }

        //will be main product item
        $properties = array('http://schema.org/AggregateRating', MageWorx_SeoMarkup_Helper_Data::OFFER);
        if (!$this->_isNotInsideTypes($node, $properties)) {
            return false;
        }


        if (!$this->_isInsideTypes($node, array('http://schema.org/Product'))) {
            return false;
        }

        return $node;
    }

    protected function _getItemValues()
    {
        return array(
            Mage::helper('mageworx_seomarkup')->getDescriptionValue($this->_product)
        );
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $parentNode = $this->_findParentContainer($node);
        if ($parentNode) {
            $parentNode->itemprop = "description";
            return true;
        }
        return false;
    }

    protected function _checkBlockType()
    {

    }

}