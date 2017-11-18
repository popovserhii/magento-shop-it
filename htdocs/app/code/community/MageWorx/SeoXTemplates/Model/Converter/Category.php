<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Converter_Category extends MageWorx_SeoXTemplates_Model_Converter
{
    /**
     * Retrieve converted string by template code
     * @param array $vars
     * @param string $templateCode
     * @return string
     */
    protected function __convert($vars, $templateCode)
    {
        $convertValue = $templateCode;

        foreach ($vars as $key => $params) {

            if (!$this->_isDynamically && $this->_issetDynamicAttribute($params['attributes'])) {
                $value = $key;
            } else {
                foreach ($params['attributes'] as $attributeCode) {

                    switch ($attributeCode) {
                        case 'category':
                            $value = $this->_convertName();
                            break;
                        case 'price':
                        case 'special_price':
                            break;
                        case 'parent_category':
                            $value = $this->_convertParentCategory();
                            break;
                        case 'categories':
                            $value = $this->_convertCategories();
                            break;
                        case 'subcategories':
                            $value = $this->_convertSubCategories();
                            break;
                        case 'store_view_name':
                            $value = $this->_convertStoreViewName();
                            break;
                        case 'store_name':
                            $value = $this->_convertStoreName();
                            break;
                        case 'website_name':
                            $value = $this->_convertWebsiteName();
                            break;
                        default:
                            if (strpos($attributeCode, 'filter_') === 0) {
                                $value = $this->_convertFilter($attributeCode);
                            } else {
                                $value = $this->_convertAttribute($attributeCode);
                            }
                            break;
                    }

                    if ($value) {
                        $prefix = $this->_helperConverter->randomizePrefix($params['prefix']);
                        $suffix = $this->_helperConverter->randomizeSuffix($params['suffix']);
                        $value  = $prefix . $value . $suffix;
                        break;
                    }

                }
            }

            $convertValue = str_replace($key, $value, $convertValue);
        }

        return $this->_render($convertValue);
    }

    /**
     * @param string $templateCode
     * @return bool
     */
    protected function _stopProccess($templateCode)
    {
        if (!$this->_isDynamically) {
            return false;
        }

        $isNotFound = true;

        if ($this->_issetDynamicAttribute(array($templateCode), false)) {
            $isNotFound = false;
        }

        return $isNotFound;
    }

    /**
     * @param array $attributes
     * @param boolean $isStrict
     * @return bool
     */
    protected function _issetDynamicAttribute($attributes, $isStrict = true)
    {
        foreach ($attributes as $attribute) {

            if ($isStrict) {
                if (strpos(trim($attribute), 'filter_') === 0) {
                    return true;
                }
            } else {
                if (strpos(trim($attribute), 'filter_') !== 0) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function _convertFilter($attributeCode)
    {
        $attributeCode = str_replace('filter_', '', $attributeCode);

        if (!$attributeCode) {
            return '';
        }

        $value = '';
        $currentFiltersData = Mage::helper('mageworx_seoall/layeredFilter')->getLayeredNavigationFiltersData();

        if (is_array($currentFiltersData) && count($currentFiltersData) > 0) {

            foreach ($currentFiltersData as $filter) {

                if ($attributeCode == 'all' || $attributeCode == $filter['code']) {
                    $value .= $filter['name'] . " " . strip_tags($filter['label'] . ', ');
                }
            }
        }

        return rtrim($value, ' ,');
    }

    protected function _convertName()
    {
        return $this->_item->getName();
    }

    protected function _convertParentCategory()
    {
        $value = '';
        $parentId = $this->_item->getParentId();
        if ($parentId) {
            if ($parentId !== Mage::app()->getWebsite(Mage::app()->getStore($this->_item->getStoreId())->getWebsite()->getId())->getDefaultStore()->getRootCategoryId()) {

                if (is_callable(array(Mage::getResourceModel('catalog/category'), 'getAttributeRawValue'))) {
                    $value = trim(Mage::getResourceModel('catalog/category')
                            ->getAttributeRawValue($parentId, 'name', $this->_item->getStoreId()));
                } else {
                    $category = Mage::getModel('catalog/category')->setStoreId($this->_item->getStoreId())->load($parentId);
                    $value    = trim($category->getName());
                }
            }
        }
        return $value;
    }

    protected function _convertCategories()
    {
        $value     = '';
        $separator = ' ' . Mage::helper('mageworx_seoxtemplates/config')->getTitleSeparator() . ' ';
        $paths     = explode('/', $this->_item->getPath());
        $paths     = (is_array($paths)) ? array_slice($paths, 1) : $this->_item->getParentCategories();

        if (is_array($paths)) {
            if (Mage::helper('mageworx_seoxtemplates/config')->isCropRootCategory($this->_item->getStoreId())) {
                array_shift($paths);
            }
            foreach ($paths as $category) {
                $categoryId = is_object($category) ? $category->getId() : $category;
                
                if (is_callable(array(Mage::getResourceModel('catalog/category'), 'getAttributeRawValue'))) {
                    $path[] = trim(Mage::getResourceModel('catalog/category')
                            ->getAttributeRawValue($categoryId, 'name', $this->_item->getStoreId()));
                } else {
                    $category = Mage::getModel('catalog/category')->setStoreId($this->_item->getStoreId())->load($categoryId);
                    $path[]   = trim($category->getName());
                }
            }
        }

        if (!empty($path) && is_array($path) && count($path) > 0) {
            $path  = array_filter($path);
            $value = join($separator, array_reverse($path));
        }

        return $value;
    }

    protected function _convertSubCategories()
    {
        $value     = '';
        $separator = ' ' . Mage::helper('mageworx_seoxtemplates/config')->getTitleSeparator() . ' ';
        
        $childIds = $this->_item->getChildren();

        if(!$childIds){
            return $value;
        }

        $names = array();
        $childCategories = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($this->_item->getStoreId())
            ->addAttributeToSelect(array('name'))
            ->addAttributeToFilter('entity_id', array('in' => explode(',', $childIds)))
            ->addAttributeToFilter('is_active', 1);

        foreach($childCategories as $category){
            $names[] = $category->getName();
        }

        if (!empty($names) && is_array($names)) {
            $names  = array_filter($names);
            $value = join($separator, $names);
        }

        return $value;
    }

    protected function _convertStoreViewName()
    {
        return Mage::app()->getStore($this->_item->getStoreId())->getName();
    }

    protected function _convertStoreName()
    {
        return Mage::app()->getStore($this->_item->getStoreId())->getGroup()->getName();
    }

    protected function _convertWebsiteName()
    {
        return Mage::app()->getStore($this->_item->getStoreId())->getWebsite()->getName();
    }

    protected function _convertAttribute($attributeCode)
    {
        $value = '';
        if ($attribute = $this->_item->getResource()->getAttribute($attributeCode)) {
            $value = $attribute->getSource()->getOptionText($this->_item->getData($attributeCode));
        }
        if (!$value) {
            $value = $this->_item->getData($attributeCode);
        }
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return $value;
    }

    protected function _render($convertValue)
    {
        return trim($convertValue);
    }

}
