<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoCrossLinks_Model_Source_Title
{
    const USE_CROSSLINK_TITLE_ONLY = 0;
    const USE_NAME_ALWAYS          = 1;
    const USE_NAME_IF_EMPTY_TITLE  = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::USE_CROSSLINK_TITLE_ONLY, 'label'=>Mage::helper('mageworx_seocrosslinks')->__("Don't Use")),
            array('value' => self::USE_NAME_IF_EMPTY_TITLE,  'label'=>Mage::helper('mageworx_seocrosslinks')->__('For Blank')),
            array('value' => self::USE_NAME_ALWAYS,          'label'=>Mage::helper('mageworx_seocrosslinks')->__('For All')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::USE_CROSSLINK_TITLE_ONLY => Mage::helper('mageworx_seocrosslinks')->__("Don't Use"),
            self::USE_NAME_IF_EMPTY_TITLE  => Mage::helper('mageworx_seocrosslinks')->__('For Blank'),
            self::USE_NAME_ALWAYS          => Mage::helper('mageworx_seocrosslinks')->__('For All'),
        );
    }

}
