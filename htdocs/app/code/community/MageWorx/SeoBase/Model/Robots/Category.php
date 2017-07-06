<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Robots_Category extends MageWorx_SeoBase_Model_Robots_Abstract
{
    protected function _getRobots()
    {
        $this->_isSetNoindexByLimit();
        $category = Mage::registry('current_category');
        if (is_object($category) && $category->getId()) {
            $maxFilterCount      = $this->_helperData->getCountFiltersForNoindex();
            $appliedFilters      = Mage::getSingleton('catalog/layer')->getState()->getFilters();
            $countAppliedFilters = (is_array($appliedFilters)) ? count($appliedFilters) : false;

            $filterCodesAsString = $this->_getCurrentFilterCodesAsString();

            if ($filterCodesAsString) {
                $attributeSettings = Mage::helper('mageworx_seobase')->getAttributeRobotsSettings();
                if (!empty($attributeSettings[$filterCodesAsString])) {
                    return $attributeSettings[$filterCodesAsString];
                }
            }
            
            if ($maxFilterCount !== false && $countAppliedFilters && $countAppliedFilters >= $maxFilterCount) {
                return 'NOINDEX, FOLLOW';
            }

            if ($this->_isSetNoindexByLimit()) {
                return 'NOINDEX, FOLLOW';
            }

            if ($category->getMetaRobots()) {
                return $category->getMetaRobots();
            }
        }
        return $this->_getRobotsBySettings();
    }

    protected function _isSetNoindexByLimit()
    {
        if (!$this->_helperData->isUseNoindexByLimit()) {
            return false;
        }

        $toolbar = Mage::app()->getLayout()->getBlock('product_list_toolbar');

        if (!is_object($toolbar)) {
            return false;
        }

        $availableLimit = $toolbar->getAvailableLimit($toolbar);

        if (!$availableLimit) {
            return false;
        }

        if($toolbar->getCurrentMode() == 'list'){
            $defByToolbar = $toolbar->getDefaultListPerPage();
            $default      = $defByToolbar ? $defByToolbar : Mage::getStoreConfig('catalog/frontend/list_per_page');
        }
        elseif ($toolbar->getCurrentMode() == 'grid') {
            $defByToolbar = $toolbar->getDefaultGridPerPage();
            $default      = $defByToolbar ? $defByToolbar : Mage::getStoreConfig('catalog/frontend/grid_per_page');
        }

        if (!array_key_exists($default, $availableLimit)) {
            return false;
        }

        if ($default && $default != $toolbar->getLimit()) {
            return ($toolbar->getLimit() == 'all' && Mage::helper('mageworx_seobase')->isUseLimitAll()) ? false : true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function _getCurrentFilterCodesAsString()
    {
        /** @var MageWorx_SeoAll_Helper_LayeredFilter $lnHelper */
        $lnHelper   = Mage::helper('mageworx_seoall/layeredFilter');
        $filterData = $lnHelper->getLayeredNavigationFiltersData();

        $codes = array();
        if (is_array($filterData) && count($filterData)) {

            foreach ($filterData as $filter) {
                if (!empty($filter['code'])) {
                    $codes[] = $filter['code'];
                }
            }
            sort($codes);
        }

        return implode('+', $codes);
    }
}