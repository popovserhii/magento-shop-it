<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Source_RedirectTo
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('mageworx_seoredirects')->__('Product Category')),
            array('value' => 1, 'label' => Mage::helper('mageworx_seoredirects')->__('Priority Category')),
        );
    }
}