<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED                          = 'mageworx_seo/richsnippets/enable_all';

    const XML_PATH_WEBSITE_RICHSNIPPET_ENABLED      = 'mageworx_seo/richsnippets/enable_website';
    const XML_PATH_WEBSITE_OPENGRAPH_ENABLED        = 'mageworx_seo/richsnippets/enable_website_og';
    const XML_PATH_WEBSITE_OPENGRAPH_LOGO           = 'mageworx_seo/richsnippets/website_og_logo';

    const XML_PATH_WEBSITE_TWITTER_ENABLED          = 'mageworx_seo/richsnippets/enable_website_tw';
    const XML_PATH_WEBSITE_TWITTER_USERNAME         = 'mageworx_seo/richsnippets/website_tw_username';
    const XML_PATH_WEBSITE_TWITTER_LOGO             = 'mageworx_seo/richsnippets/website_tw_logo';

    const XML_PATH_WEBSITE_ABOUT                    = 'mageworx_seo/richsnippets/website_about';
    const XML_PATH_WEBSITE_SEARCH                   = 'mageworx_seo/richsnippets/website_use_search';
    const XML_PATH_WEBSITE_NAME                     = 'mageworx_seo/richsnippets/website_name';
    const XML_PATH_MAGENTO_WEBSITE_NAME             = 'general/store_information/name';

    const XML_PATH_SELLER_ENABLED                   = 'mageworx_seo/richsnippets/enable_seller';
    const XML_PATH_SELLER_TYPE                      = 'mageworx_seo/richsnippets/seller_type';
    const XML_PATH_SELLER_NAME                      = 'mageworx_seo/richsnippets/seller_name';
    const XML_PATH_SELLER_DESCRIPTION               = 'mageworx_seo/richsnippets/seller_description';
    const XML_PATH_SELLER_PHONE                     = 'mageworx_seo/richsnippets/seller_phone';
    const XML_PATH_SELLER_FAX                       = 'mageworx_seo/richsnippets/seller_fax';
    const XML_PATH_SELLER_EMAIL                     = 'mageworx_seo/richsnippets/seller_email';
    const XML_PATH_SELLER_LOCATION                  = 'mageworx_seo/richsnippets/seller_location';
    const XML_PATH_SELLER_REGION                    = 'mageworx_seo/richsnippets/seller_region';
    const XML_PATH_SELLER_STREET                    = 'mageworx_seo/richsnippets/seller_street';
    const XML_PATH_SELLER_POST_CODE                 = 'mageworx_seo/richsnippets/seller_post_code';

    const XML_PATH_BREADCRUMBS_ENABLED              = 'mageworx_seo/richsnippets/enable_breadcrumbs';
    const XML_PATH_BREADCRUMBS_METHOD               = 'mageworx_seo/richsnippets/breadcrumbs_method';

    const XML_PATH_PAGE_OPENGRAPH_ENABLED           = 'mageworx_seo/richsnippets/enable_page_og';
    const XML_PATH_PAGE_TWITTER_ENABLED             = 'mageworx_seo/richsnippets/enable_page_tw';
    const XML_PATH_PAGE_TWITTER_USERNAME            = 'mageworx_seo/richsnippets/page_tw_username';

    const XML_PATH_CATEGORY_RICHSNIPPET_ENABLED     = 'mageworx_seo/richsnippets/enable_category_rs';
    const XML_PATH_CATEGORY_OPENGRAPH_ENABLED       = 'mageworx_seo/richsnippets/enable_category_og';
    const XML_PATH_CATEGORY_USE_OFFERS              = 'mageworx_seo/richsnippets/product_offer_in_category';
    const XML_PATH_CATEGORY_ROBOTS_RESTRICTION      = 'mageworx_seo/richsnippets/category_robots_restriction';

    const XML_PATH_PRODUCT_ENABLED                  = 'mageworx_seo/richsnippets/enable_product';
    const XML_PATH_PRODUCT_OPENGRAPH_ENABLED        = 'mageworx_seo/richsnippets/enable_product_og';
    const XML_PATH_PRODUCT_TWITTER_ENABLED          = 'mageworx_seo/richsnippets/enable_product_tw';
    const XML_PATH_PRODUCT_TWITTER_USERNAME         = 'mageworx_seo/richsnippets/product_tw_username';
    const XML_PATH_PRODUCT_METHOD                   = 'mageworx_seo/richsnippets/product_method';
    const XML_PATH_PRODUCT_META_PRICE               = 'mageworx_seo/richsnippets/add_product_meta_price';
    const XML_PATH_RATING_ENABLED                   = 'mageworx_seo/richsnippets/enable_rating';
    const XML_PATH_BEST_RATING                      = 'mageworx_seo/richsnippets/best_rating';
    const XML_PATH_SKU_ENABLED                      = 'mageworx_seo/richsnippets/enable_sku';
    const XML_PATH_SKU_CODE                         = 'mageworx_seo/richsnippets/attribute_code_sku';
    const XML_PATH_DESCRIPTION_CODE                 = 'mageworx_seo/richsnippets/attribute_code_description';
    const XML_PATH_PAYMENT_ENABLED                  = 'mageworx_seo/richsnippets/enable_payment';
    const XML_PATH_PAYMENT_LIST                     = 'mageworx_seo/richsnippets/payment_list';
    const XML_PATH_DELIVERY_ENABLED                 = 'mageworx_seo/richsnippets/enable_delivery';
    const XML_PATH_DELIVERY_LIST                    = 'mageworx_seo/richsnippets/delivery_list';
    const XML_PATH_COLOR_ENABLED                    = 'mageworx_seo/richsnippets/enable_color';
    const XML_PATH_COLOR_CODE                       = 'mageworx_seo/richsnippets/attribute_code_color';
    const XML_PATH_HEIGHT_ENABLED                   = 'mageworx_seo/richsnippets/enable_height';
    const XML_PATH_HEIGHT_CODE                      = 'mageworx_seo/richsnippets/attribute_code_height';
    const XML_PATH_WIDTH_ENABLED                    = 'mageworx_seo/richsnippets/enable_width';
    const XML_PATH_WIDTH_CODE                       = 'mageworx_seo/richsnippets/attribute_code_width';
    const XML_PATH_DEPTH_ENABLED                    = 'mageworx_seo/richsnippets/enable_depth';
    const XML_PATH_DEPTH_CODE                       = 'mageworx_seo/richsnippets/attribute_code_depth';
    const XML_PATH_WEIGHT_ENABLED                   = 'mageworx_seo/richsnippets/enable_weight';
    const XML_PATH_WEIGHT_UNIT                      = 'mageworx_seo/richsnippets/weight_unit';
    const XML_PATH_MANUFACTURER_ENABLED             = 'mageworx_seo/richsnippets/enable_manufacturer';
    const XML_PATH_MANUFACTURER_CODE                = 'mageworx_seo/richsnippets/attribute_code_manufacturer';
    const XML_PATH_BRAND_ENABLED                    = 'mageworx_seo/richsnippets/enable_brand';
    const XML_PATH_BRAND_CODE                       = 'mageworx_seo/richsnippets/attribute_code_brand';
    const XML_PATH_MODEL_ENABLED                    = 'mageworx_seo/richsnippets/enable_model';
    const XML_PATH_MODEL_CODE                       = 'mageworx_seo/richsnippets/attribute_code_model';
    const XML_PATH_GTIN_ENABLED                     = 'mageworx_seo/richsnippets/enable_gtin';
    const XML_PATH_GTIN_CODE                        = 'mageworx_seo/richsnippets/attribute_code_gtin';
    const XML_PATH_DIMENSIONS_ENABLED               = 'mageworx_seo/richsnippets/enable_dimensions';
    const XML_PATH_DIMENSIONS_UNIT                  = 'mageworx_seo/richsnippets/dimensions_unit';
    const XML_PATH_CONDITION_ENABLED                = 'mageworx_seo/richsnippets/enable_condition';
    const XML_PATH_CONDITION_CODE                   = 'mageworx_seo/richsnippets/attribute_code_condition';
    const XML_PATH_CONDITION_NEW                    = 'mageworx_seo/richsnippets/condition_value_new';
    const XML_PATH_CONDITION_REF                    = 'mageworx_seo/richsnippets/condition_value_refurbished';
    const XML_PATH_CONDITION_USED                   = 'mageworx_seo/richsnippets/condition_value_used';
    const XML_PATH_CONDITION_DAMAGED                = 'mageworx_seo/richsnippets/condition_value_damaged';
    const XML_PATH_CONDITION_DEFAULT                = 'mageworx_seo/richsnippets/condition_value_default';
    const XML_PATH_CATEGORY_ENABLED                 = 'mageworx_seo/richsnippets/enable_category';
    const XML_PATH_CATEGORY_DEEPEST                 = 'mageworx_seo/richsnippets/category_deepest';
    const XML_PATH_ENABLED_CUSTOM_PROPERTIES        = 'mageworx_seo/richsnippets/enable_product_custom_properties';
    const XML_PATH_CUSTOM_PROPERTIES                = 'mageworx_seo/richsnippets/product_custom_properties';
    const XML_PATH_SAME_AS_LINKS                    = 'mageworx_seo/richsnippets/same_as_links';

    const XML_PATH_EVENT_ENABLED                    = 'mageworx_seo/richsnippets/enable_event';
    const XML_PATH_EVENT_ATTRIBUTE_SET              = 'mageworx_seo/richsnippets/event_attribute_set';
    const XML_PATH_EVENT_BEST_RATING                = 'mageworx_seo/richsnippets/event_best_rating';
    const XML_PATH_EVENT_DESCRIPTION_CODE           = 'mageworx_seo/richsnippets/event_attribute_code_description';
    const XML_PATH_EVENT_LOCATION_NAME_CODE         = 'mageworx_seo/richsnippets/attribute_code_location_name';
    const XML_PATH_EVENT_ADDRESS_LOCALITY_CODE      = 'mageworx_seo/richsnippets/attribute_code_address_locality';
    const XML_PATH_EVENT_ADDRESS_STREET_CODE        = 'mageworx_seo/richsnippets/attribute_code_address_street';
    const XML_PATH_EVENT_START_DATE_CODE            = 'mageworx_seo/richsnippets/attribute_code_start_date';

    public function isEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /** Website section **/

    public function isWebsiteOpenGraphEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_WEBSITE_OPENGRAPH_ENABLED);
    }

    public function getFacebookLogoFile()
    {
        return Mage::getStoreConfig(self::XML_PATH_WEBSITE_OPENGRAPH_LOGO);
    }

    public function isWebsiteTwitterEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_WEBSITE_TWITTER_ENABLED);
    }

    public function getTwitterLogoFile()
    {
        return Mage::getStoreConfig(self::XML_PATH_WEBSITE_TWITTER_LOGO);
    }

    public function getWebsiteTwitterUsername()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_WEBSITE_TWITTER_USERNAME));
    }

    public function isWebsiteRichsnippetEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_RICHSNIPPET_WEBSITE_ENABLED);
    }

    public function getWebSiteName()
    {
        $storeName = trim(Mage::getStoreConfig(self::XML_PATH_WEBSITE_NAME));
        return $storeName ? $storeName : trim(Mage::getStoreConfig(self::XML_PATH_MAGENTO_WEBSITE_NAME));
    }

    public function getWebsiteAboutInfo()
    {
        return (trim(Mage::getStoreConfig(self::XML_PATH_WEBSITE_ABOUT)));
    }

    public function isAddWebsiteSearchAction()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_WEBSITE_SEARCH);
    }

    /** Seller section **/

    public function isSellerRichsnippetEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_SELLER_ENABLED);
    }

    public function getSellerName()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_NAME));
    }

    public function getSellerType()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_TYPE));
    }

    public function getSellerDescription()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_DESCRIPTION));
    }

    public function getSellerPhone()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_PHONE));
    }

    public function getSellerFax()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_FAX));
    }

    public function getSellerEmail()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_EMAIL));
    }

    public function getSellerLocation()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_LOCATION));
    }

    public function getSellerRegionAddress()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_REGION));
    }

    public function getSellerStreetAddress()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_STREET));
    }

    public function getSellerPostCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SELLER_POST_CODE));
    }

    /** Breadcrumbs **/

    public function isBreadcrumbsRichsnippetEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_BREADCRUMBS_ENABLED);
    }

    public function isBreadcrumbsInjectionMicrodataMethod()
    {
        return (MageWorx_SeoMarkup_Model_System_Config_Source_BreadcrumbsMethod::RICHSNIPPET_INJECTION_MICRODATA ==
            Mage::getStoreConfig(self::XML_PATH_BREADCRUMBS_METHOD));
    }

    public function isBreadcrumbsJsonLdMethod()
    {
         return (MageWorx_SeoMarkup_Model_System_Config_Source_BreadcrumbsMethod::RICHSNIPPET_JSON_LD ==
            Mage::getStoreConfig(self::XML_PATH_BREADCRUMBS_METHOD));
    }

    /** Page **/

    public function isPageOpenGraphEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_PAGE_OPENGRAPH_ENABLED);
    }

    public function isPageTwitterEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_PAGE_TWITTER_ENABLED);
    }

    public function getPageTwitterUsername()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_PAGE_TWITTER_USERNAME));
    }

    /**Category**/

    public function isCategoryRichsnippetEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_RICHSNIPPET_ENABLED);
    }

    public function isCategoryOpenGraphEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_OPENGRAPH_ENABLED);
    }

    public function isUseOfferForCategoryProducts()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_USE_OFFERS);
    }

    public function isUseCategoryRobotsRestriction()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_ROBOTS_RESTRICTION);
    }

    /** Product **/

    public function isProductOpenGraphEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_OPENGRAPH_ENABLED);
    }

    public function isProductTwitterEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_TWITTER_ENABLED);
    }

     public function getProductTwitterUsername()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_PRODUCT_TWITTER_USERNAME));
    }

    public function isProductRichsnippetEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_ENABLED);
    }

    public function isProductInjectionMicrodataMethod()
    {
        return (MageWorx_SeoMarkup_Model_System_Config_Source_ProductMethod::RICHSNIPPET_INJECTION_MICRODATA ==
            Mage::getStoreConfig(self::XML_PATH_PRODUCT_METHOD));
    }

    public function isProductAdditionalBlockMicrodataMethod()
    {
        return (MageWorx_SeoMarkup_Model_System_Config_Source_ProductMethod::RICHSNIPPET_BLOCK_MICRODATA ==
            Mage::getStoreConfig(self::XML_PATH_PRODUCT_METHOD));
    }

    public function isProductJsonLdMethod()
    {
         return (MageWorx_SeoMarkup_Model_System_Config_Source_ProductMethod::RICHSNIPPET_JSON_LD ==
            Mage::getStoreConfig(self::XML_PATH_PRODUCT_METHOD));
    }

    public function isAddMetaPrice()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PRODUCT_META_PRICE);
    }

    public function isRichsnippetCategoryEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_ENABLED);
    }

    public function isRichsnippetCategoryDeepest()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CATEGORY_DEEPEST);
    }

    public function isRichsnippetSellerEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SELLER_ENABLED);
    }

    public function isRichsnippetConditionEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CONDITION_ENABLED);
    }

    public function getDescriptionAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_DESCRIPTION_CODE));
    }

    public function getSkuAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_SKU_CODE));
    }

    public function isRatingEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_RATING_ENABLED);
    }

    public function getBestRating()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_BEST_RATING));
    }

    public function getConditionAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_CONDITION_CODE));
    }

    public function getConditionValueForNew()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_CONDITION_NEW));
    }

    public function getConditionValueForRefurbished()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_CONDITION_REF));
    }

    public function getConditionValueForDamaged()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_CONDITION_DAMAGED));
    }

    public function getConditionValueForUsed()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_CONDITION_USED));
    }

    public function getConditionDefaultValue()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_CONDITION_DEFAULT));
    }

    public function isRichsnippetSkuEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_SKU_ENABLED);
    }

    public function isRichsnippetPaymentEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_PAYMENT_ENABLED);
    }

    public function getPaymentMethodList()
    {
        $paymentsString = trim(Mage::getStoreConfig(self::XML_PATH_PAYMENT_LIST));
        $paymentsList   = array_map('trim', explode(',', $paymentsString));
        return array_filter($paymentsList);
    }

    public function isRichsnippetDeliveryEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_DELIVERY_ENABLED);
    }

    public function getDeliveryMethodList()
    {
        $deliveriesString = trim(Mage::getStoreConfig(self::XML_PATH_DELIVERY_LIST));
        $deliveryList = array_map('trim', explode(',', $deliveriesString));
        return array_filter($deliveryList);
    }

    public function isRichsnippetColorEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_COLOR_ENABLED);
    }

    public function getColorAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_COLOR_CODE));
    }

    public function isRichsnippetManufacturerEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_MANUFACTURER_ENABLED);
    }

    public function getManufacturerAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_MANUFACTURER_CODE));
    }

    public function isRichsnippetBrandEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_BRAND_ENABLED);
    }

    public function getBrandAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_BRAND_CODE));
    }

    public function isRichsnippetModelEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_MODEL_ENABLED);
    }

    public function getModelAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_MODEL_CODE));
    }

    public function isRichsnippetGtinEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_GTIN_ENABLED);
    }

    public function getGtinAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_GTIN_CODE));
    }

    public function isRichsnippetDimensionsEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_DIMENSIONS_ENABLED);
    }

    public function getRichsnippetDimensionsUnit()
    {
        $unit = trim(Mage::getStoreConfig(self::XML_PATH_DIMENSIONS_UNIT));
        if (preg_match('/^[a-zA-Z]+$/', $unit)) {
            return $unit;
        }
        return false;
    }

    public function isRichsnippetHeightEnabled()
    {
        if ($this->isRichsnippetDimensionsEnabled()) {
            return (bool) Mage::getStoreConfigFlag(self::XML_PATH_HEIGHT_ENABLED);
        }
        return false;
    }

    public function getHeightAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_HEIGHT_CODE));
    }

    public function isRichsnippetWidthEnabled()
    {
        if ($this->isRichsnippetDimensionsEnabled()) {
            return (bool) Mage::getStoreConfigFlag(self::XML_PATH_WIDTH_ENABLED);
        }
        return false;
    }

    public function getWidthAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_WIDTH_CODE));
    }

    public function isRichsnippetDepthEnabled()
    {
        if ($this->isRichsnippetDimensionsEnabled()) {
            return (bool) Mage::getStoreConfigFlag(self::XML_PATH_DEPTH_ENABLED);
        }
        return false;
    }

    public function getDepthAttributeCode()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_DEPTH_CODE));
    }

    public function isRichsnippetWeightEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_WEIGHT_ENABLED);
    }

    public function getRichsnippetWeightUnit()
    {
        return trim(Mage::getStoreConfig(self::XML_PATH_WEIGHT_UNIT));
    }

    public function getCustomProperties()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED_CUSTOM_PROPERTIES)) {
            return array();
        }

        $string = trim(Mage::getStoreConfig(self::XML_PATH_CUSTOM_PROPERTIES));
        $pairArray = array_filter(preg_split('/\r?\n/', $string));
        $pairArray = array_filter(array_map('trim', $pairArray));

        $ret = array();

        foreach ($pairArray as $pair) {
            $pair = trim($pair, ',');
            $explode = explode(',', $pair);
            if (is_array($explode) && count($explode) >= 2) {
                $key = trim($explode[0]);
                $val = trim($explode[1]);
                if ($key && $val) {
                    $ret[$key] = $val;
                }
            }
        }
        return $ret;
    }

    public function getSameAsLinks()
    {
        $linksString = trim(Mage::getStoreConfig(self::XML_PATH_SAME_AS_LINKS));
        $linksArray = array_filter(preg_split('/\r?\n/', $linksString));
        $linksArray = array_map('trim', $linksArray);
        return array_filter($linksArray);
    }

    public function isEventRichsnippetEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return Mage::getStoreConfigFlag(self::XML_PATH_EVENT_ENABLED);
    }

    public function getEventAttributeSets()
    {
        $linksString = trim(Mage::getStoreConfig(self::XML_PATH_EVENT_ATTRIBUTE_SET));
        $linksArray = array_filter(preg_split('/\r?\n/', $linksString));
        $linksArray = array_map('trim', $linksArray);
        return array_filter($linksArray);
    }

    public function getEventBestRating()
    {
        return Mage::getStoreConfig(self::XML_PATH_EVENT_BEST_RATING);
    }

    public function getEventDescriptionCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_EVENT_DESCRIPTION_CODE);
    }

    public function getEventLocationName()
    {
        return Mage::getStoreConfig(self::XML_PATH_EVENT_LOCATION_NAME_CODE);
    }

    public function getEventAddressLocalityCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_EVENT_ADDRESS_LOCALITY_CODE);
    }

    public function getEventAddressStreetCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_EVENT_ADDRESS_STREET_CODE);
    }

    public function getEventStartDateCode()
    {
        return Mage::getStoreConfig(self::XML_PATH_EVENT_START_DATE_CODE);
    }

}