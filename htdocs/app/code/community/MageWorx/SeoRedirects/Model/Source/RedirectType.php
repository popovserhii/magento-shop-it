<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Source_RedirectType
{
    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 301, 'label' => Mage::helper('mageworx_seoredirects')->__('301 Moved Permanently')),
            array('value' => 302, 'label' => Mage::helper('mageworx_seoredirects')->__('302 Found')),
        );
    }
}