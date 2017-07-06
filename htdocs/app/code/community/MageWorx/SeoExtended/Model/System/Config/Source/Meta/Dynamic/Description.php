<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoExtended_Model_System_Config_Source_Meta_Dynamic_Description
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'on', 'label' => Mage::helper('catalog')->__('Yes')),
            array('value' => 'on_for_empty', 'label' => Mage::helper('seoextended')->__('For Products with Empty Meta Description')),
            array('value' => 'off', 'label' => Mage::helper('catalog')->__('No')),
        );
    }
}