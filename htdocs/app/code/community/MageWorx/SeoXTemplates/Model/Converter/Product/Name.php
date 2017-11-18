<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoXTemplates_Model_Converter_Product_Name extends MageWorx_SeoXTemplates_Model_Converter_Product
{
    protected function _convertStoreViewName()
    {
        return '';
    }

    protected function _convertStoreName()
    {
        return '';
    }

    protected function _convertWebsiteName()
    {
        return '';
    }

    protected function _convertCategory()
    {
        return '';
    }

    protected function _convertCategories()
    {
        return '';
    }

    /**
     *
     * @param string $convertValue
     * @return string
     */
    protected function _render($convertValue)
    {
        $convertValue = parent::_render($convertValue);
        return strip_tags($convertValue);
    }
}