<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_XSitemap_Helper_Adapter_Seobase extends Mage_Core_Helper_Abstract
{
    public function isSeoBaseAvailable()
    {
        if ((string) Mage::getConfig()->getModuleConfig('MageWorx_SeoBase')->active == 'true') {
            return true;
        }
        return false;
    }

    public function getSeoBaseHelper($functionName = null, $helperName = null)
    {
        if ($this->isSeoBaseAvailable()) {
            if(is_null($helperName)){
                $helperName = 'mageworx_seobase';
            }
            $seoHelper = @Mage::helper($helperName);
            if ($seoHelper && $seoHelper instanceof Mage_Core_Helper_Abstract) {
                if ($functionName) {
                    if (!is_callable(array($seoHelper, $functionName))) {
                        return false;
                    }
                }
                return $seoHelper;
            }
        }
        return false;
    }

    public function getSeoAlternateFinalCodes($type, $storeId)
    {
        if($this->isSeoBaseAvailable()){
            $alternateHelper = $this->getSeoBaseHelper('getAlternateFinalCodes', 'mageworx_seobase/hreflang');
            return $alternateHelper->getAlternateFinalCodes($type, $storeId);
        }
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function isSeoBaseCanonicalUrlEnabled($storeId = null)
    {
        $helper = $this->getSeoBaseHelper('isCanonicalUrlEnabled');
        if ($helper) {
            return $helper->isCanonicalUrlEnabled($storeId);
        }
        return false;
    }

    public function getSeoBaseProductCanonicalType($storeId = null)
    {
        $helper = $this->getSeoBaseHelper('getProductCanonicalType');
        if ($helper) {
            return $helper->getProductCanonicalType($storeId);
        }
        return false;
    }
}