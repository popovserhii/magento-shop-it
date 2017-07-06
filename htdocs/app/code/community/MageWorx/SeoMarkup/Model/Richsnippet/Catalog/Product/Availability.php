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
 * Information is looked for in a product html, but availability tag is inserted in a offer tag (near a price tag).
 * Because in a default template availability information outside of a offer tag.
 *
 * If price richsnippet fail (status flag in register), product won't be rendered and this code won't be executed.
 *
 * @see MageWorx_SeoMarkup_Model_Catalog_Product_Richsnippet_Product
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Availability extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected $_availabilityStatus = false;

    protected function _beforeRender($html)
    {
        $string = (is_object($html)) ? $html->innertext : $html;

        if (strpos($string, Mage::helper('catalog')->__('In stock')) !== false) {
            $this->_availabilityStatus = 'in';
        }
        elseif (strpos($string, Mage::helper('catalog')->__('Out of stock')) !== false) {
            $this->_availabilityStatus = 'out';
        }

        if ($this->_availabilityStatus) {
            $this->_availabilityStatus = ($this->_product->getIsInStock()) ? 'in' : 'out';
        }
        
        if ($this->_product->getTypeId() == 'grouped') {
            $this->_availabilityStatus = ($this->_product->getIsInStock()) ? 'in' : 'out';
        }
        return parent::_beforeRender($html);
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        return ($this->_availabilityStatus) ? true : false;
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        if ($this->_availabilityStatus == 'in') {
            $node->innertext = $node->innertext .
                '<link itemprop="availability" href="' . MageWorx_SeoMarkup_Helper_Data::IN_STOCK . '">' . "\n";
        }
        elseif ($this->_availabilityStatus == 'out') {
            $node->innertext = $node->innertext .
                '<link itemprop="availability" href="' . MageWorx_SeoMarkup_Helper_Data::OUT_OF_STOCK . '">' . "\n";
        }
    }

    protected function _getItemConditions()
    {
        return array("*[itemtype=" . MageWorx_SeoMarkup_Helper_Data::OFFER . "]");
    }

    protected function _checkBlockType()
    {
        return true;
    }
}