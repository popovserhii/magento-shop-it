<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Config extends Mage_Core_Helper_Abstract
{
    const MAX_DEFAULT_LENGTH_META_TITLE                = 70;
    const MAX_DEFAULT_LENGTH_META_DESCRIPTION          = 150;

    const XML_PATH_TEMPLATE_LIMIT              = 'mageworx_seo/seoxtemplates/template_limit';
    const XML_PATH_CROP_ROOT_CATEGORY          = 'mageworx_seo/seoxtemplates/crop_root_category';

    const XML_PATH_ENABLE_PRODUCT_SEO_NAME     = 'mageworx_seo/seoxtemplates/use_product_seo_name';
    const XML_PATH_ENABLE_CATEGORY_SEO_NAME    = 'mageworx_seo/seoxtemplates/use_category_seo_name';

    const XML_PATH_CROP_META_TITLE             = 'mageworx_seo/seoxtemplates/crop_meta_title';
    const XML_PATH_CROP_META_DESCRIPTION       = 'mageworx_seo/seoxtemplates/crop_meta_description';
    const XML_PATH_MAX_LENGTH_META_TITLE       = 'mageworx_seo/seoxtemplates/max_title_length';
    const XML_PATH_MAX_LENGTH_META_DESCRIPTION = 'mageworx_seo/seoxtemplates/max_description_length';

    const XML_PATH_ENABLE_CRON_NOTIFY          = 'mageworx_seo/seoxtemplates/enabled_cron_notify';
    const XML_PATH_ERROR_TEMPLATE              = 'mageworx_seo/seoxtemplates/error_email_template';
    const XML_PATH_ERROR_IDENTITY              = 'mageworx_seo/seoxtemplates/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT             = 'mageworx_seo/seoxtemplates/error_email';


    /**
     * @param int|null $store
     * @return bool
     */
    public function isCropMetaTitle($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CROP_META_TITLE, $store);
    }

    /**
     * Retrieve max length for meta title for specified view mode
     *
     * @param int|null $store
     * @return int
     */
    public function getMaxLengthMetaTitle($store = null)
    {
        $max = (int)Mage::getStoreConfig(self::XML_PATH_MAX_LENGTH_META_TITLE, $store);
        if(!$max){
            return self::MAX_DEFAULT_LENGTH_META_TITLE;
        }
        return $max;
    }

    /**
     * @param int|null $store
     * @return bool
     */
    public function isCropMetaDescription($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CROP_META_DESCRIPTION, $store);
    }

    /**
     * Retrieve max length for meta description for specified view mode
     *
     * @param int|null $store
     * @return int
     */
    public function getMaxLengthMetaDescription($store = null)
    {
        $max = (int)Mage::getStoreConfig(self::XML_PATH_MAX_LENGTH_META_DESCRIPTION, $store);
        if(!$max){
            return self::MAX_DEFAULT_LENGTH_META_DESCRIPTION;
        }
        return $max;
    }

    /**
     * Use product seo name attribute instead name
     *
     * @param int|null $store
     * @return bool
     */
    public function isUseProductSeoName($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_PRODUCT_SEO_NAME, $store);
    }

    /**
     * Use category seo name attribute instead name
     *
     * @param int|null $store
     * @return bool
     */
    public function isUseCategorySeoName($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_CATEGORY_SEO_NAME, $store);
    }

    /**
     * Retrive quantity of templates items for a generation step
     *
     * @return int
     */
    public function getTemplateLimitForCurrentStore()
    {
        $limit = (int) Mage::getStoreConfig(self::XML_PATH_TEMPLATE_LIMIT, Mage::app()->getRequest()->getParam('store', 0));
        return ($limit) ? $limit : 50;
    }

    /**
     * Is crop root category from template
     * @param int|null $store
     * @return bool
     */
    public function isCropRootCategory($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CROP_ROOT_CATEGORY, $store);
    }

    /**
     * Is enabled e-mail notification for generation by cron
     * @param int|null $store
     * @return bool
     */
    public function isEnabledCronNotify($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_CRON_NOTIFY, $store);
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getErrorEmailTemplate($store = null)
    {
        return 'mageworx_seoxtemplates_generate_error_email_template';
        //return Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE, $store);
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getErrorEmailIdentity($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY, $store);
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getErrorEmailRecipient($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT, $store);
    }

    /**
     *
     * @param int|null $store
     * @return bool
     */
    public function isShowCommentAboutCategory($store = null)
    {
        if(!$this->useCategoriesPathInProductUrl($store)){
            return true;
        }
        return false;
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function useCategoriesPathInProductUrl($store = null)
    {
        return Mage::getStoreConfigFlag('catalog/seo/product_use_categories', $store);
    }

    /**
     *
     * @param int|null $store
     * @return string
     */
    public function getTitleSeparator()
    {
        return (string) Mage::getStoreConfig('catalog/seo/title_separator');
    }

}
