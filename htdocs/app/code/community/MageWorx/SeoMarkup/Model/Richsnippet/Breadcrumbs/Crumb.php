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
 * @see MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Breadcrumbs
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Breadcrumbs_Crumb extends MageWorx_SeoMarkup_Model_Richsnippet_Abstract
{
    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $parentNode = $this->_findParentContainer($node);
        
        if($parentNode){
            $grandParentNode = $this->_findParentContainer($parentNode);

            if ($grandParentNode) {

                $grandParentNode->itemtype   = "http://schema.org/BreadcrumbList";
                $grandParentNode->itemscope  = "";

                $parentNode->itemprop        = "itemListElement";
                $parentNode->itemscope       = "";
                $parentNode->itemtype        = "http://schema.org/ListItem";

                $node->itemprop  = "item";
                $node->innertext = $node->innertext . '<meta itemprop="name" content = "' . $node->plaintext . '"/>';
                return true;
            }
        }
        
        return false;
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        $parentNode = $this->_findParentContainer($node);
        if (!$parentNode || $parentNode->itemtype) {
            return false;
        }
        return $node;
    }

    protected function _checkBlockType()
    {
        return true;
    }

    protected function _getItemConditions()
    {
        return array("a[href=*]");
    }

}