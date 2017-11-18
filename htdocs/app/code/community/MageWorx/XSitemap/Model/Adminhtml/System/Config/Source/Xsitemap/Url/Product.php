<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Model_Adminhtml_System_Config_Source_Xsitemap_Url_Product
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'yes', 'label' => Mage::helper('xsitemap')->__('Yes')),
            array('value' => 'no', 'label' => Mage::helper('xsitemap')->__('No')),
            array('value' => 'canonical', 'label' => Mage::helper('xsitemap')->__('Use Canonical URL'))
        );
    }

}
