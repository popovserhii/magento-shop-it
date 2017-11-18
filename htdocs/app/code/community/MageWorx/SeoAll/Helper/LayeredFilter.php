<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_LayeredFilter extends Mage_Core_Helper_Abstract
{
    /**
     * Retrive specific filters data as array (use for canonical url)
     * @return array | false
     */
    public function getLayeredNavigationFiltersData()
    {
        $filterData     = array();
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();

        if (is_array($appliedFilters) && count($appliedFilters) > 0) {
            foreach ($appliedFilters as $item) {

                if (is_null($item->getFilter()->getData('attribute_model'))) {
                    //Ex: If $item->getFilter()->getRequestVar() == 'cat'
                    $use_in_canonical = 0;
                    $position         = 0;
                }
                else {
                    $attributeModel = $item->getFilter()->getAttributeModel();
                    if (is_callable(array($attributeModel, 'getLayeredNavigationCanonical'))) {
                        $use_in_canonical = $attributeModel->getLayeredNavigationCanonical();
                    }else{
                        $use_in_canonical = 0;
                    }

                    if(is_callable(array($attributeModel, 'getPosition'))){
                        $position = $attributeModel->getPosition();
                    }else{
                        $position = false;
                    }
                }

                $filterData[] = array(
                    'name'             => $item->getName(),
                    'label'            => $item->getLabel(),
                    'code'             => $item->getFilter()->getRequestVar(),
                    'use_in_canonical' => $use_in_canonical,
                    'position'         => $position
                );
            }
        }
        return (count($filterData) > 0) ? $filterData : false;
    }

    /**
     * @return bool
     */
    public function isApplyedLayeredNavigationFilters()
    {
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
        return (is_array($appliedFilters) && count($appliedFilters) > 0) ? true : false;
    }
}