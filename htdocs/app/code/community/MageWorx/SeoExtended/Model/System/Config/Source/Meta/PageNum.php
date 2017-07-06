<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoExtended_Model_System_Config_Source_Meta_PageNum
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'begin', 'label' => Mage::helper('seoextended')->__('At the Beginning')),
            array('value' => 'end', 'label' => Mage::helper('seoextended')->__('At the end')),
            array('value' => 'off', 'label' => Mage::helper('catalog')->__('No'))
        );
    }
}