<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_System_Config_Source_Cms_RelationWay
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_ID,
                'label' => Mage::helper('mageworx_seobase')->__('By ID')
            ),
            array(
                'value' => MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_URLKEY,
                'label' => Mage::helper('mageworx_seobase')->__('By URL Key')
            ),
            array(
                'value' => MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_IDENTIFIER,
                'label' => Mage::helper('mageworx_seobase')->__('By Hreflang Key')
            ),
        );
    }
}