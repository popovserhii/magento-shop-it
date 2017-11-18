<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SEOSUITE_LN_FRIENDLY_URLS_ENABLED = 'mageworx_seo/seofriendlyln/enable_ln_friendly_urls';
    const XML_PATH_SEOSUITE_LNAVIGATION_IDENTIFIER   = 'mageworx_seo/seofriendlyln/layered_identifier';

    protected $_enterpriseSince113 = null;

    public function isLNFriendlyUrlsEnabled()
    {
        $fullActionName = Mage::helper('mageworx_seoall/request')->getCurrentFullActionName();

        if ($fullActionName && $fullActionName !== 'catalog_category_view') {
            return false;
        }
        return Mage::getStoreConfigFlag(self::XML_PATH_SEOSUITE_LN_FRIENDLY_URLS_ENABLED);
    }

    public function getLayeredNavigationIdentifier()
    {
        $identifier = trim(Mage::getStoreConfig(self::XML_PATH_SEOSUITE_LNAVIGATION_IDENTIFIER));
        $identifier = strtolower(trim($identifier, '/'));
        if (preg_match('/^[a-z]+$/', $identifier)) {
            return $identifier;
        }
        return 'l';
    }

    public function getAttributeValueDelimiter()
    {
        $delimeter = trim(Mage::getStoreConfig('mageworx_seo/seofriendlyln/layered_separatort'));
        return $delimeter ? $delimeter : ':';
    }

    public function getAttributeParamDelimiter()
    {
        return Mage::getStoreConfigFlag('mageworx_seo/seofriendlyln/layered_hide_attributes') ? '/' : $this->getAttributeValueDelimiter();
    }

    public function getPagerUrlFormat()
    {
        if ($this->isLNFriendlyUrlsEnabled()) {
            $pagerUrlFormat = trim(Mage::getStoreConfig('mageworx_seo/seofriendlyln/pager_url_format'));
            if (strpos($pagerUrlFormat, '[page_number]') !== false) {
                return $pagerUrlFormat;
            }
        }
        return false;
    }

}