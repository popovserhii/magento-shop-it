<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Model_Converter_Product_Metakeywords extends MageWorx_SeoXTemplates_Model_Converter_Product
{
    protected $_dynamicVariables = array('category', 'categories');

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