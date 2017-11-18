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
 * @see MageWorx_SeoMarkup_Block_Review_Helper
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Review extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected $_reviewData = array();

    protected function _beforeRender($html)
    {
        /**
         * Only main product
         */
        if (!Mage::registry('current_product') || $this->_product->getId() != Mage::registry('current_product')->getId()) {
            return false;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isRatingEnabled()) {
            return false;
        }

        $this->_reviewData = Mage::helper('mageworx_seomarkup')->getAggregateRatingData($this->_product);

        if (!Mage::helper('mageworx_seomarkup')->isValidReviewData($this->_reviewData)) {
            return false;
        }

        return true;
    }

    protected function _checkBlockType()
    {
        return true;
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        $parentNode = $this->_findParentContainer($node);
        if (!$parentNode) {
            return false;
        }
        $grandParentNode = $this->_findParentContainer($parentNode);
        if (!$grandParentNode) {
            return false;
        }
        if (!$this->_isNotInsideTypes($node)) {
            return false;
        }
        return true;
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $parentNode            = $this->_findParentContainer($node);
        $parentNode->itemtype  = 'http://schema.org/AggregateRating';
        $parentNode->itemscope = "";
        $parentNode->itemprop  = 'aggregateRating';
        $this->_addRatingValueMetaTag($node, $this->_reviewData['ratingValue']);
        $this->_addReviewCountMetaTag($node, $this->_reviewData['reviewCount']);
        $this->_addBestRatingMetaTag($node, $this->_reviewData['bestRating']);
        $this->_addWorstRatingMetaTag($node, $this->_reviewData['worstRating']);
        return true;
    }

    protected function _getItemConditions()
    {
        $conditions   = array();
        $reviewModel  = Mage::getModel('review/review_summary');
        $rating       = $reviewModel->setStoreId(Mage::app()->getStore()->getId())->load($this->_product->getId())->getRatingSummary();
        $conditions[] = "div[class=rating], div[style=width:{$rating}%]";
        return $conditions;
    }

    protected function _afterRender()
    {
        $report = new Varien_Object(array('status' => 'success'));
        Mage::register('mageworx_richsnippet_aggregate_rating_report', $report, true);
        return parent::_afterRender();
    }

    protected function _addRatingValueMetaTag($node, $ratingValue)
    {
        $node->outertext = '<meta itemprop="ratingValue" content="' . $ratingValue . '"/>' . $node->outertext;
    }

    protected function _addReviewCountMetaTag($node, $reviewCount)
    {
        $node->outertext = '<meta itemprop="reviewCount" content="' . $reviewCount . '"/>' . $node->outertext;
    }

    protected function _addWorstRatingMetaTag($node, $worstRating = 0)
    {
        $node->outertext = '<meta itemprop="worstRating" content="' . $worstRating . '">' . $node->outertext;
    }

    protected function _addBestRatingMetaTag($node, $bestRating = 100)
    {
        $node->outertext = '<meta itemprop="bestRating" content="' . $bestRating . '">' . $node->outertext;
    }
}
