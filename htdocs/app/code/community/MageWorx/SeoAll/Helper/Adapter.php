<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Adapter extends Mage_Core_Helper_Abstract
{
    public function getDateForFilename()
    {
        return Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
    }

    /**
     * @param int|null $storeId
     * @return boolean
     */
    public function isReviewFriendlyUrlEnable($storeId = null)
    {
        if ($this->isModuleEnabled('MageWorx_SeoBase')) {
            return Mage::helper('mageworx_seobase')->isReviewFriendlyUrlEnable($storeId);
        }
        return false;
    }
}