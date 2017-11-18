<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Adminhtml_System_Config_Source_Switcher_Scope
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('core')->__('Global')),
            array('value' => '1', 'label' => Mage::helper('core')->__('Website')),
        );
    }

}