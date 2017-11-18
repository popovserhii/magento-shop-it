<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Core_Url_Rewrite_Collection extends Mage_Core_Model_Mysql4_Url_Rewrite_Collection
{

    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()),
                array('*', new Zend_Db_Expr('LENGTH(request_path)')));
        return $this;
    }

    public function sortByLength($spec = 'DESC')
    {
        $this->getSelect()->order(new Zend_Db_Expr('LENGTH(request_path) ' . $spec));
        return $this;
    }

    public function sortByCategoryCount($spec = 'DESC')
    {
        $this->getSelect()->order(new Zend_Db_Expr("char_length(`request_path`) - char_length(replace(`request_path`,'/','')) " . $spec));
        return $this;
    }

    public function filterAllByProductId($productId)
    {
        if ($productId != null) {
            $this->getSelect()->where('product_id = ?', $productId, Zend_Db::INT_TYPE);
            return $this;
        }
    }

    protected function _filterLongestUrl() {
        $this->getSelect()->where('category_id IS NOT NULL');
        $this->getSelect()->where('is_system = 1');
        $this->sortByLength('DESC');
    }

    protected function _filterShortestUrl() {
        $this->getSelect()->where('category_id IS NOT NULL');
        $this->getSelect()->where('is_system = 1');
        $this->sortByLength('ASC');
    }

    protected function _filterRootUrl() {
        $this->getSelect()->where('category_id IS NULL AND is_system = 1');
    }

    protected function _filterLongestUrlByCategoryCount() {
        $this->getSelect()->where('category_id IS NOT NULL AND is_system = 1');
        $this->sortByCategoryCount();
    }

    protected function _filterShortestUrlByCategoryCount() {
        $this->getSelect()->where('category_id IS NOT NULL AND is_system = 1');
        $this->sortByCategoryCount('ASC');
    }

    public function filterByIdPath($idPath)
    {
        $this->getSelect()->where('id_path = ?', $idPath);
        return $this;
    }

    public function groupByUrl()
    {
        $this->getSelect()->group('request_path');
        return $this;
    }

    public function filterCanonicalUrl($canonicalType)
    {
        switch ($canonicalType) {
            case MageWorx_SeoBase_Model_Canonical_Product::LONGEST_BY_URL:
                $this->_filterLongestUrl();
                break;
            case MageWorx_SeoBase_Model_Canonical_Product::SHORTEST_BY_URL:
                $this->_filterShortestUrl();
                break;
            case MageWorx_SeoBase_Model_Canonical_Product::ROOT:
                $this->_filterRootUrl();
                break;
            case MageWorx_SeoBase_Model_Canonical_Product::LONGEST_BY_CATEGORY:
                $this->_filterLongestUrlByCategoryCount();
                break;
            case MageWorx_SeoBase_Model_Canonical_Product::SHORTEST_BY_CATEGORY:
                $this->_filterShortestUrlByCategoryCount();
                break;
        }
        return $this;
    }

}