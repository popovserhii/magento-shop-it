<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Adminhtml_System_Config_Frontend_Duplicate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (Mage::getSingleton('adminhtml/config_data')->getStore()) {
            $storeId = Mage::getModel('core/store')->load(Mage::getSingleton('adminhtml/config_data')->getStore())->getId();
        }
        elseif (Mage::getSingleton('adminhtml/config_data')->getWebsite()) {
            $websiteId = Mage::getModel('core/website')->load(Mage::getSingleton('adminhtml/config_data')->getWebsite())->getId();
            $storeId = Mage::app()->getWebsite($websiteId)->getDefaultStore()->getId();
        }
        else {
            $storeId = 0;
        }

        $element->setValue(Mage::getStoreConfigFlag('catalog/seo/product_use_categories', $storeId));
        return parent::render($element);
    }
}