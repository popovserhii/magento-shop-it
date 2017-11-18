<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoExtended_Model_System_Config_Source_Meta_CropKeywords
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'yes', 'label' => Mage::helper('seoextended')->__('Yes (Ignore List Available)')),
            array('value' => 'for_empty',  'label' => Mage::helper('seoextended')->__('Yes, if Empty Content')),
            array('value' => 'no',      'label' => Mage::helper('catalog')->__('No'))
        );
    }
}