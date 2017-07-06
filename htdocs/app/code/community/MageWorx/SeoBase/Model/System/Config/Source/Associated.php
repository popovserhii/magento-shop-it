<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_Associated
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label' => Mage::helper('mageworx_seobase')->__('Use Default')),
            array('value' => 'use_parent', 'label' => Mage::helper('mageworx_seobase')->__('Parent Product')),
        );
    }
}