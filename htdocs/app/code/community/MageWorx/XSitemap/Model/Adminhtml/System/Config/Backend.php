<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Adminhtml_System_Config_Backend extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value < 0 || $value > 1) {
            throw new Exception(Mage::helper('xsitemap')->__('Priority must be between 0 and 1'));
        }
        elseif (($value == 0) && !($value === '0' || $value === '0.0')) {
            throw new Exception(Mage::helper('xsitemap')->__('Priority must be between 0 and 1'));
        }
        return $this;
    }

}
