<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoExtended_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_OPTIMIZED_URLS_ENABLED                   = 'mageworx_seo/seoextended/optimized_urls';
    const XML_PATH_DYNAMIC_META_TITLE                       = 'mageworx_seo/seoextended/status_dynamic_meta_title';
    const XML_PATH_DYNAMIC_META_DESCRIPTION                 = 'mageworx_seo/seoextended/status_dynamic_meta_description';
    const XML_PATH_DYNAMIC_META_KEYWORDS                    = 'mageworx_seo/seoextended/status_dynamic_meta_keywords';
    const XML_PATH_PAGE_NUM_FOR_TITLE                       = 'mageworx_seo/seoextended/status_pager_num_for_title';
    const XML_PATH_PAGE_NUM_FOR_DESCRIPTION                 = 'mageworx_seo/seoextended/status_pager_num_for_description';
    const XML_PATH_CUT_TITLE_PREFIX_SUFFIX                  = 'mageworx_seo/seoextended/cut_title_prefix_and_suffix';
    const XML_PATH_EXTENDED_META_TITLE_FOR_LN_ENABLED       = 'mageworx_seo/seoextended/extended_category_layered_navigation_meta_title';
    const XML_PATH_EXTENDED_META_DESCRIPTION_FOR_LN_ENABLED = 'mageworx_seo/seoextended/extended_category_layered_navigation_meta_description';
    const XML_PATH_EXTENDED_META_CROP_KEYWORDS              = 'mageworx_seo/seoextended/crop_meta_keywords';
    const XML_PATH_EXTENDED_META_IGNORE_PAGES_FOR_KEYWORDS  = 'mageworx_seo/seoextended/ignore_pages_crop_meta_keywords';

    /**
     * @param int|null $store
     * @return bool
     */
    public function isOptimizedUrlsEnabled($store = null)
    {
        if (Mage::getStoreConfigFlag(self::XML_PATH_OPTIMIZED_URLS_ENABLED, $store)
            && !Mage::helper('mageworx_seoall/version')->isEeRewriteActive()
        ) {
            return true;
        }
        return false;
    }

    public function getStatusPagerNumForMeta($metaType)
    {
        switch ($metaType) {
            case 'title':
                $ret = $this->getStatusPagerNumForMetaTitle();
                break;
            case 'description':
                $ret = $this->getStatusPagerNumForMetaDescription();
                break;
            default:
                $ret = null;
        }
        return $ret;
    }

    /**
     * @param type $type
     * @return string
     */
    public function getStatusDynamicMetaType($metaType)
    {
        switch ($metaType) {
            case 'title':
                $ret = $this->getStatusDynamicMetaTitle();
                break;
            case 'description':
                $ret = $this->getStatusDynamicMetaDescription();
                break;
            case 'keywords':
                $ret = $this->getStatusDynamicMetaKeywords();
                break;
            default: $ret = null;
        }
        return $ret;
    }

    public function getStatusPagerNumForMetaTitle()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PAGE_NUM_FOR_TITLE);
    }

    public function getStatusPagerNumForMetaDescription()
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_PAGE_NUM_FOR_DESCRIPTION);
    }

    public function isCutPrefixSuffixFromProductAndCategoryPages()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUT_TITLE_PREFIX_SUFFIX);
    }

    public function getStatusDynamicMetaTitle()
    {
        return ((string) Mage::getStoreConfig(self::XML_PATH_DYNAMIC_META_TITLE));
    }

    public function getStatusDynamicMetaDescription()
    {
        return ((string) Mage::getStoreConfig(self::XML_PATH_DYNAMIC_META_DESCRIPTION));
    }

    public function getStatusDynamicMetaKeywords()
    {
        return ((string) Mage::getStoreConfig(self::XML_PATH_DYNAMIC_META_KEYWORDS));
    }

    public function isExtendedMetaTitleForLNEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_EXTENDED_META_TITLE_FOR_LN_ENABLED);
    }

    public function isExtendedMetaDescriptionForLNEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_EXTENDED_META_DESCRIPTION_FOR_LN_ENABLED);
    }

    public function getStatusCropMetaKeywordsTag()
    {
        return ((string) Mage::getStoreConfig(self::XML_PATH_EXTENDED_META_CROP_KEYWORDS));
    }

    public function getIgnorePagesForMetaKeywords()
    {
        $ignorePages = array_filter(preg_split('/\r?\n/',
                Mage::getStoreConfig(self::XML_PATH_EXTENDED_META_IGNORE_PAGES_FOR_KEYWORDS)));
        return array_map('trim', $ignorePages);
    }
}
