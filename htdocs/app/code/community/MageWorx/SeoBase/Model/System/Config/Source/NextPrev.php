<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_System_Config_Source_NextPrev
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => MageWorx_SeoBase_Model_NextPrev_Abstract::ENABLE_NEXT_PREV,
                'label' => Mage::helper('catalog')->__('Yes')
            ),
            array(
                'value' => MageWorx_SeoBase_Model_NextPrev_Category::ENABLE_IF_NO_FILTERS,
                'label' => Mage::helper('mageworx_seobase')->__('Yes, except filtered pages of the layered navigation')
            ),
            array(
                'value' => MageWorx_SeoBase_Model_NextPrev_Abstract::DISABLE_NEXT_PREV,
                'label' => Mage::helper('catalog')->__('No')
            ),
        );
    }
}