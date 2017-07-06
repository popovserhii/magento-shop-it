<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Model_System_Config_Source_Weight_Unit
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'lb', 'label' => Mage::helper('mageworx_seomarkup')->__('lb')),
            array('value' => 'kg', 'label' => Mage::helper('mageworx_seomarkup')->__('kg'))
        );
    }

}

?>