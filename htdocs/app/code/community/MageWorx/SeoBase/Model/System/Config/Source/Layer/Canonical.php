<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_Layer_Canonical
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => Mage::helper('mageworx_seobase')->__('Use Config')),
            array('value' => 1, 'label' => Mage::helper('mageworx_seobase')->__('Filtered Page')),
            array('value' => 2, 'label' => Mage::helper('mageworx_seobase')->__('Current Category'))
        );
    }

}