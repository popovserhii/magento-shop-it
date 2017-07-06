<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoExtended_Helper_Adapter_Friendlyln extends Mage_Core_Helper_Abstract
{
    public function isAvailableSeoFriendlyLN()
    {
        if ((string)Mage::getConfig()->getModuleConfig('MageWorx_SeoFriendlyLN')->active == 'true'){
            return true;
        }
    }
}