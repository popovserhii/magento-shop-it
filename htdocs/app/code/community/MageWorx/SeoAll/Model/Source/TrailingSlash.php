<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Model_Source_TrailingSlash
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Abstract::TRAILING_SLASH_CROP,
                'label' => Mage::helper('mageworx_seoall')->__('Crop')
            ),
            array(
                'value' => MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Abstract::TRAILING_SLASH_ADD,
                'label' => Mage::helper('mageworx_seoall')->__('Add')
            ),
            array(
                'value' => MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Abstract::TRAILING_SLASH_DEFAULT,
                'label' => Mage::helper('mageworx_seoall')->__('Default')
            ),
        );
    }
}