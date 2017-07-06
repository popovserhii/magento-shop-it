<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoMarkup_Model_System_Config_Source_Attributes
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $attributes = Mage::getResourceModel('catalog/product_attribute_collection');
            $options = $this->_toOptionHash($attributes);
            array_unshift($options, Mage::helper('adminhtml')->__('-- Please Select --'));
            $this->_options = $options;
        }

        return $this->_options;
    }

    /**
     * Convert items array to hash
     *
     * return items hash
     * array($value => $label)
     *
     * @param   string $valueField
     * @param   string $labelField
     * @return  array
     */
    protected function _toOptionHash($collection)
    {
        $res = array();
        foreach ($collection as $item) {
            $frontendLabel = $item->getData('frontend_label') ? ' (' .   $item->getData('frontend_label')  . ')' : '';
            $res[$item->getData('attribute_code')] = $item->getData('attribute_code') . $frontendLabel;
        }
        return $res;
    }

}