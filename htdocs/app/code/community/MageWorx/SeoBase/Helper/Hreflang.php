<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Helper_Hreflang extends Mage_Core_Helper_Abstract
{
    const SCOPE_GLOBAL  = 0;
    const SCOPE_WEBSITE = 1;

    const CMS_RELATION_BY_ID = 0;
    const CMS_RELATION_BY_URLKEY = 1;
    const CMS_RELATION_BY_IDENTIFIER = 2;

    const XML_PATH_ALTERNATE_ENABLED                       = 'mageworx_seo/alternate_url/enabled';
    const XML_PATH_ALTERNATE_CATEGORY_ENABLED              = 'mageworx_seo/alternate_url/enabled_category';
    const XML_PATH_ALTERNATE_PRODUCT_ENABLED               = 'mageworx_seo/alternate_url/enabled_product';
    const XML_PATH_ALTERNATE_CMS_ENABLED                   = 'mageworx_seo/alternate_url/enabled_cms';
    const XML_PATH_ALTERNATE_SCOPE                         = 'mageworx_seo/alternate_url/scope';
    const XML_PATH_ALTERNATE_USE_ISSET_LANG_CODE           = 'mageworx_seo/alternate_url/use_isset_lang_code';
    const XML_PATH_ALTERNATE_LANG_CODE                     = 'mageworx_seo/alternate_url/lang_code';
    const XML_PATH_ALTERNATE_COUNTRY_CODE_ENABLE           = 'mageworx_seo/alternate_url/country_code_enable';
    const XML_PATH_ALTERNATE_USE_ISSET_COUNTRY_CODE        = 'mageworx_seo/alternate_url/use_isset_country_code';
    const XML_PATH_ALTERNATE_COUNTRY_CODE                  = 'mageworx_seo/alternate_url/country_code';
    const XML_PATH_MAGENTO_LANGUAGE_CODE                   = 'general/locale/code';
    const XML_PATH_MAGENTO_COUNTRY_CODE                    = 'general/country/default';
    const XML_PATH_XDEFAULT_GLOBAL                         = 'mageworx_seo/alternate_url/x_default_global';
    const XML_PATH_XDEFAULT_WEBSITE                        = 'mageworx_seo/alternate_url/x_default_website';
    const XML_PATH_CMS_RELATION_WAY                        = 'mageworx_seo/alternate_url/cms_relation_way';


    public function isAlternateTagsEnabled($type, $storeId)
    {
        if (Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_ENABLED, $storeId)) {
            switch ($type) {
                case 'product':
                    return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_PRODUCT_ENABLED, $storeId);
                    break;
                case 'category':
                    return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_CATEGORY_ENABLED, $storeId);
                    break;
                case 'cms':
                    return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_CMS_ENABLED, $storeId);
                default:
                    return true;
                    break;
            }
        }
        return false;
    }

    public function getCmsPageRelationWay()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_CMS_RELATION_WAY);
    }

    public function isAlternateTagsScope()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_SCOPE);
    }

    public function getAlternateScope()
    {
        if ($this->isAlternateTagsScope() == self::SCOPE_GLOBAL) {
            return 'global';
        }
        return 'website';
    }

    public function isUseIssetLanguageCode($storeId)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_USE_ISSET_LANG_CODE, $storeId);
    }

    public function getLanguageCode($storeId)
    {
        if ($this->isUseIssetLanguageCode($storeId)) {
            return $this->_convertLocaleToLanguageCode($storeId);
        }
        return Mage::getStoreConfig(self::XML_PATH_ALTERNATE_LANG_CODE, $storeId);
    }

    public function isCountryCodeEnabled($storeId)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_COUNTRY_CODE_ENABLE, $storeId);
    }

    public function isUseIssetCountryCode($storeId)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALTERNATE_USE_ISSET_COUNTRY_CODE, $storeId);
    }

    public function getXDefaultStoreIds($type, $storeId)
    {
        if ($this->isAlternateTagsScope() == self::SCOPE_GLOBAL) {
            return array($this->getDefaultAlternateStoreIdForWebsiteScope($storeId));
        }
        ///Website Scope
        else {
            $xdefaultStoreIds = $this->getDefaultAlternateStoreIdsForGlobalScope($storeId);
            $websiteStoreIds = $this->getWebsiteStoreIdsByStoreId($type, $storeId);

            $storeIds = array_intersect($xdefaultStoreIds, $websiteStoreIds);

            //if (!empty($storeIds)) {
                return $storeIds;
                //return array_shift($storeIds);
            //}
            return null;
        }
    }

    public function getCountryCode($storeId)
    {
        if ($this->isUseIssetCountryCode($storeId)) {
            return $this->_convertCountryToCountryCode($storeId);
        }
        return Mage::getStoreConfig(self::XML_PATH_ALTERNATE_COUNTRY_CODE, $storeId);
    }

    public function getAlternateFinalCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }
        $langCodes = $this->getAlternateLanguageCodes($type, $storeId);

        $countryCodes = $this->getAlternateCountryCodes($type, $storeId);

        $alternateFinalCodes = array();
        $xdefaultStoreIds = $this->getXDefaultStoreIds($type, $storeId);
        $xdefaultStoreId = array_shift($xdefaultStoreIds);

        foreach ($langCodes as $storeId => $langCode) {
            if (!empty($countryCodes[$storeId])) {
                $langCode = $langCode . '-' . $countryCodes[$storeId];
            }
            if ($storeId == $xdefaultStoreId) {
                $langCode = 'x-default';
            }
            $alternateFinalCodes[$storeId] = $langCode;
        }

        $this->_deleteDuplicateCodes($alternateFinalCodes);

        return $alternateFinalCodes;
    }

    public function getAlternateRawCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }
        $langCodes = $this->getAlternateLanguageCodes($type, $storeId);

        $countryCodes = $this->getAlternateCountryCodes($type, $storeId);

        $alternateRawCodes = array();
        $xdefaultStoreIds = $this->getXDefaultStoreIds($type, $storeId);

        foreach ($langCodes as $storeId => $langCode) {
            if (!empty($countryCodes[$storeId])) {
                $langCode = $langCode . '-' . $countryCodes[$storeId];
            }
            if (in_array($storeId, $xdefaultStoreIds)) {
                $langCode = 'x-default';
            }
            $alternateRawCodes[$storeId] = $langCode;
        }

        return $alternateRawCodes;
    }

    public function getAlternateLanguageCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }
        $storeLangCodes = array();
        $storeIds = $this->getAlternateStoreIds($type, $storeId);
        foreach ($storeIds as $storeId) {
            $storeLangCodes[$storeId] = $this->getLanguageCode($storeId);
        }

        return $storeLangCodes;
    }

    public function getAlternateCountryCodes($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }
        $storeCountryCodes = array();

        $storeIds = $this->getAlternateStoreIds($type, $storeId);
        foreach ($storeIds as $storeId) {
            if ($this->isCountryCodeEnabled($storeId)) {
                $storeCountryCodes[$storeId] = $this->getCountryCode($storeId);
            }
        }

        return $storeCountryCodes;
    }

    public function getAlternateStoreIds($type, $storeId)
    {
        ///Global Scope
        if (!$this->isAlternateTagsScope()) {
            return $this->getAllEnabledStoreIds($type);
        }
        ///Website Scope
        else {
            return $this->getWebsiteStoreIdsByStoreId($type, $storeId);
        }
    }

    public function getAllEnabledStoreIds($type)
    {
        $stores = $this->getAllEnabledStore($type);
        $storeIds = array();
        foreach ($stores as $store)
        {
            $storeIds[] = $store->getStoreId();
        }
        return $storeIds;
    }

    public function getAllEnabledStore($type)
    {
        $allStores = Mage::app()->getStores();
        $stores    = array();
        foreach ($allStores as $store)
        {
            $storeId = $store->getStoreId();
            if ($store->getIsActive() == 1 && $this->isAlternateTagsEnabled($type, $storeId)) {
                $stores[] = $store;
            }
        }
        return $stores;
    }

    public function getWebsiteStoreIdsByStoreId($type, $storeId = null)
    {
        if (!$storeId) {
            $website = Mage::app()->getStore()->getWebsite();
        } else {
            $website = Mage::getModel('core/store')->load($storeId)->getWebsite();
        }

        return $this->filterValidStoreIds($type, $website->getStoreIds());
    }

    public function filterValidStoreIds($type, $storeIds)
    {
        $validIds = $this->getAllEnabledStoreIds($type);
        return array_intersect($storeIds, $validIds);
    }

    protected function _convertLocaleToLanguageCode($storeId)
    {
        list($magentoLangCode) = explode('_', Mage::getStoreConfig(self::XML_PATH_MAGENTO_LANGUAGE_CODE, $storeId));
        return $magentoLangCode;
    }

    protected function _convertCountryToCountryCode($storeId)
    {
        $magentoCountryCode = Mage::getStoreConfig(self::XML_PATH_MAGENTO_COUNTRY_CODE, $storeId);
        return $magentoCountryCode;
    }

    public function getDefaultAlternateStoreIdForWebsiteScope($storeId)
    {
        return Mage::getStoreConfig(self::XML_PATH_XDEFAULT_GLOBAL, $storeId);
    }

    public function getDefaultAlternateStoreIdsForGlobalScope($storeId)
    {
        $storeIdsAsString = Mage::getStoreConfig(self::XML_PATH_XDEFAULT_WEBSITE, $storeId);
        $xdefaultStoreIds = explode(',', $storeIdsAsString);
        if (empty($xdefaultStoreIds)) {
            return array();
        }
        return $xdefaultStoreIds;
    }

    /**
     * @todo Log if isset identical values.
     * @param type $array
     */
    protected function _deleteDuplicateCodes(&$array)
    {
        $array = array_unique($array);
    }

    public function getBaseStoreUrls()
    {
        $allStores = Mage::app()->getStores();
        $baseStoreUrls = array();
        foreach ($allStores as $id)
        {
                $storeId = Mage::app()->getStore($id)->getId();
            $url = rtrim(Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK), '/') . '/';
            $baseStoreUrls[$storeId] = $url;
        }
        return $baseStoreUrls;
    }


    public function getAlternateHtml($altLinksCollection, $alternateCodes)
    {
        $altLinks = array();
        foreach ($altLinksCollection as $store => $altUrl) {
            $hreflang = $alternateCodes[$store];
            $altLinks[] = '<link rel="alternate" hreflang="' . $hreflang . '" href="' . $altUrl .'" />';
        }
        return implode("\r", $altLinks) . "\r";
    }


    public function getAlternateCmsHtml($altLinksCollection, $alternateCodes)
    {
        foreach ($altLinksCollection as $page) {
            foreach ($page['alternateUrls'] as $store => $altUrl) {
                $hreflang = $alternateCodes[$store];
                $altLinks[] = '<link rel="alternate" hreflang="' . $hreflang . '" href="' . $altUrl .'" />';
            }
        }

        return implode("\n", $altLinks);
    }

    public function getCmsIdentifierValue($page)
    {
        if (!is_object($page)) {
            return null;
        }

        if ($this->getCmsPageRelationWay() == self::CMS_RELATION_BY_ID)
        {
            return $page->getPageId();
        }
        elseif ($this->getCmsPageRelationWay() == self::CMS_RELATION_BY_URLKEY) {
            return $page->getIdentifier();
        }
        elseif ($this->getCmsPageRelationWay() == self::CMS_RELATION_BY_IDENTIFIER) {
            return $page->getMageworxHreflangIdentifier();
        }
    }

    public function isCmsHomePage($storeId, $identifier)
    {
        if (!$identifier) {
            return true;
        }

        list($homeConfig) = explode('|', Mage::getStoreConfig('web/default/cms_home_page', $storeId));

        if ($identifier == $homeConfig) {
            return true;
        }
        return false;
    }
}