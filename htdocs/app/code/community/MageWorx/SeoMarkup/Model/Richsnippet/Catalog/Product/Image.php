<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


/*
 * @see MageWorx_SeoMarkup_Model_Catalog_Product_Richsnippet_Product
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Image extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _isValidNode(simple_html_dom_node $node)
    {
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

    protected function _isInsideTypes(simple_html_dom_node $node, array $types)
    {
        $node = clone $node;
        while ($node = $node->parent) {
            foreach ($types as $key => $itemtype) {
                if ($node->itemtype == $itemtype) {
                    unset($types[$key]);
                }
            }
            if (!count($types)) {
                return true;
            }
        }
        return false;
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $node->itemprop = "image";
        return true;
    }

    protected function _getItemConditions()
    {
        return array('img');
    }

    protected function _checkBlockType()
    {

    }

}