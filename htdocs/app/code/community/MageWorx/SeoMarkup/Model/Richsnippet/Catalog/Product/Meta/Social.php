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
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Meta_Social extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $socialLinks = $this->_getSocialLinksAsString();
        if ($socialLinks) {
            $node->innertext = $node->innertext . $socialLinks;
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

    protected function _getSocialLinksAsString()
    {
        $socialLinks = Mage::helper('mageworx_seomarkup/config')->getSameAsLinks();

        if (count($socialLinks)) {
            $socialLinksString = '';
            foreach ($socialLinks as $socialLink) {
                $socialLinksString .= '<meta itemprop="sameAs" content="' . $socialLink . '">' . "\n";
            }
            return trim($socialLinksString, "\n");
        }
        return null;
    }

}