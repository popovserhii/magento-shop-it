<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Source_Yesno
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::STATUS_ENABLED, 'label' => Mage::helper('adminhtml')->__('Yes')),
            array('value' => self::STATUS_DISABLED, 'label' => Mage::helper('adminhtml')->__('No')),
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
            self::STATUS_DISABLED => Mage::helper('adminhtml')->__('No'),
            self::STATUS_ENABLED => Mage::helper('adminhtml')->__('Yes'),
        );
    }

}
