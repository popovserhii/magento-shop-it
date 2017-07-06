<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Converter_Category_Metatitle extends MageWorx_SeoXTemplates_Model_Converter_Category
{
    /**
     *
     * @param string $convertValue
     * @return string
     */
    protected function _render($convertValue)
    {
        $convertValue = parent::_render($convertValue);
        $convertValue = strip_tags($convertValue);
        if (Mage::helper('mageworx_seoxtemplates/config')->isCropMetaTitle($this->_item->getStoreId())) {
            $length       = Mage::helper('mageworx_seoxtemplates/config')->getMaxLengthMetaTitle($this->_item->getStoreId());
            $convertValue = mb_substr($convertValue, 0, $length);
        }
        return trim($convertValue);
    }
}
