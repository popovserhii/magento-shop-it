<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_ActionName
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'no', 'label' => Mage::helper('catalog')->__('No')),
            array('value' => 'source_code', 'label' => Mage::helper('mageworx_seobase')->__('Show in Source Code of Page')),
        );
    }
}
