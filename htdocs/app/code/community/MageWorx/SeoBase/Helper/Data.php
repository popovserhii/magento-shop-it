<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_CANONICAL_URL_ENABLED                = 'mageworx_seo/seobase/enabled';
    const XML_PATH_CANONICAL_URL_TYPE                   = 'mageworx_seo/seobase/product_canonical_url';
    const XML_PATH_USE_PRODUCT_CANONICAL_FOR_REVIEW     = 'mageworx_seo/seobase/use_product_canonical_for_review';
    const XML_PATH_ALLOW_FILTERS_CANONICAL              = 'mageworx_seo/seobase/enable_canonical_tag_for_layered_navigation';
    const XML_PATH_CANONICAL_ROOT_FOR_CMS_PAGE          = 'mageworx_seo/seobase/use_root_cms_for_canonical';
    const XML_PATH_CANONICAL_ASSOCIATED_PRODUCT_ENABLED = 'mageworx_seo/seobase/canonical_associated_product';
    const XML_PATH_CANONICAL_IGNORE_PAGES               = 'mageworx_seo/seobase/ignore_pages';
    const XML_PATH_CANONICAL_FOR_CONF_PRODUCT           = 'mageworx_seo/seobase/canonical_configurable';
    const XML_PATH_CANONICAL_FOR_BUNDLE_PRODUCT         = 'mageworx_seo/seobase/canonical_bundle';
    const XML_PATH_CANONICAL_FOR_GROUPED_PRODUCT        = 'mageworx_seo/seobase/canonical_grouped';
    const XML_PATH_CANONICAL_FOR_NOROUTE_PAGE           = 'mageworx_seo/seobase/canonical_noroute';
    const XML_PATH_NOINDEX_FOR_LN_COUNT                 = 'mageworx_seo/seobase/count_filters_for_noindex';
    const XML_PATH_ATTRIBUTE_SETTINGS                   = 'mageworx_seo/seobase/attribute_settings';
    const XML_PATH_NOINDEX_BY_LIMIT                     = 'mageworx_seo/seobase/noindex_by_limit';
    const XML_PATH_ROBOTS_FOR_HTTPS                     = 'mageworx_seo/seobase/https_robots';
    const XML_PATH_CANONICAL_USE_LIMIT_ALL              = 'mageworx_seo/seobase/use_limit_all';
    const XML_PATH_CANONICAL_USE_PAGER                  = 'mageworx_seo/seobase/use_pager';
    const XML_PATH_CANONICAL_CROSS_DOMAIN               = 'mageworx_seo/seobase/cross_domain';
    const XML_PATH_REVIEW_FRIENDLY_URLS                 = 'mageworx_seo/seobase/reviews_friendly_urls';


    /**
     * XML config path pages for noindex, follow robots
     */
    const XML_PATH_NOINDEX_PAGES                        = 'mageworx_seo/seobase/noindex_pages';

    /**
     * XML config path user pages for noindex, follow robots
     */
    const XML_PATH_NOINDEX_USER_PAGES                   = 'mageworx_seo/seobase/noindex_pages_user';

    /**
     * XML config path user pages for noindex, nofollow robots
     */
    const XML_PATH_NOINDEX_NOFOLLOW_USER_PAGES          = 'mageworx_seo/seobase/noindex_nofollow_pages_user';

    protected $_enterpriseSince113 = null;

    public function showFullActionName()
    {
        return Mage::getStoreConfig('mageworx_seo/tools/show_action_name');
    }

    public function getCrossDomainStoreId($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CANONICAL_CROSS_DOMAIN, $storeId);
    }

    public function isAssociatedCanonicalEnabled($storeId)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_ASSOCIATED_PRODUCT_ENABLED, $storeId);
    }

    public function isUseRootCmsPageForCanonicalUrl($storeId)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_ROOT_FOR_CMS_PAGE, $storeId);
    }

    public function isUseLimitAll($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_USE_LIMIT_ALL, $storeId);
    }

    /**
     * Check if pager parameter in canonical URL is allow
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isUsePagerInCanonical($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_USE_PAGER, $storeId);
    }

    public function getProductTypeForReplaceCanonical($storeId)
    {
        $types = array();
        switch ('use_parent') {
            case Mage::getStoreConfig(self::XML_PATH_CANONICAL_FOR_CONF_PRODUCT, $storeId):
                $types[] = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
            case Mage::getStoreConfig(self::XML_PATH_CANONICAL_FOR_BUNDLE_PRODUCT, $storeId):
                $types[] = Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
            case Mage::getStoreConfig(self::XML_PATH_CANONICAL_FOR_GROUPED_PRODUCT, $storeId):
                $types[] = Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
                break;
        }
        return $types;
    }

    /**
     * Retrive list of pages for noindex, follow robots
     *
     * @param int $storeId
     * @return bool
     */
    public function getCanonicalIgnorePages($storeId = null)
    {
        $ignorePagesString = Mage::getStoreConfig(self::XML_PATH_CANONICAL_IGNORE_PAGES, $storeId);
        $ignorePages = array_filter(preg_split('/\r?\n/', $ignorePagesString));
        return array_map('trim', $ignorePages);
    }

    /**
     * Retrive filter count for noindex pages
     *
     * @param int|null $storeId
     * @return bool|int
     */
    public function getCountFiltersForNoindex($storeId = null)
    {
        if ((string)Mage::getStoreConfig(self::XML_PATH_NOINDEX_FOR_LN_COUNT, $storeId) === '') {
            return false;
        }
        return (int)Mage::getStoreConfig(self::XML_PATH_NOINDEX_FOR_LN_COUNT, $storeId);
    }

    /**
     * Retrieve attribute meta robots settings as array 'attribute_combination' => 'meta_robots_value'
     *
     * @param null|int $storeId
     * @return array
     */
    public function getAttributeRobotsSettings($storeId = null)
    {
        $attributeSettingsAsString = Mage::getStoreConfig(self::XML_PATH_ATTRIBUTE_SETTINGS, $storeId);
        $attributeSettingsAsRawArray = array_filter(preg_split('/\r?\n/', $attributeSettingsAsString));
        $attributeSettingsAsRawArray = array_map('trim', $attributeSettingsAsRawArray);
        $attributeSettingsAsRawArray = array_map('strtolower', $attributeSettingsAsRawArray);

        $attributeSettingArray = array();

        foreach($attributeSettingsAsRawArray as $settingLine) {
            if (strpos($settingLine, ':') === false) {
                continue;
            }
            $settingLine = str_replace(' ', '', $settingLine);
            $conditionArray = explode(':', $settingLine);
            
            if (count($conditionArray) != 2) {
                continue;
            }

            $metaRobotsValue = $this->_formatMetaRobotsValue($conditionArray[0]);
            if (!$this->isValidRobots($metaRobotsValue)) {
                continue;
            }

            $attributesAsString = $conditionArray[1];
            $attributesAsString = trim($attributesAsString, ',');

            if (strpos($attributesAsString, ',') !== false) {
                $attributeCombinations = explode(',', $attributesAsString);
                $attributeCombinations = array_filter($attributeCombinations);

            } else {
                $attributeCombinations = array($attributesAsString);
            }

            if (!count($attributeCombinations)) {
                continue;
            }

            foreach ($attributeCombinations as $attributeCombination) {
                $attributeCombination = $this->_sortAttributeString($attributeCombination);
                $attributeSettingArray[$attributeCombination] = $metaRobotsValue;
            }
        }

        return $attributeSettingArray;
    }

    /**
     * @param string $attributeString
     * @return string
     */
    protected function _sortAttributeString($attributeString)
    {
        if (strpos($attributeString, '+') !== false) {
            $attributes = explode('+', $attributeString);
            $attributes = array_filter($attributes);
            if (!count($attributes)) {
                return $attributeString;
            }

            sort($attributes);
            return implode('+', $attributes);
        }

        return $attributeString;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function _formatMetaRobotsValue($value)
    {
        $value = str_replace(' ', '', $value);
        $value = str_replace(',', ', ', $value);
        return strtoupper($value);
    }

    /**
     * Check is valid meta robots value
     *
     * @param string $metaRobotsValue
     * @return bool
     */
    public function isValidRobots($metaRobotsValue)
    {
        /**
         * @var MageWorx_SeoBase_Model_Catalog_Product_Attribute_Source_Meta_Robots $robotsProvider
         */
        $robotsProvider   = Mage::getSingleton('mageworx_seobase/catalog_product_attribute_source_meta_robots');
        $robotsValidArray = $robotsProvider->getAllOptions(false, false);

        foreach ($robotsValidArray as $robots) {
            if ($robots['value'] == $metaRobotsValue) {
                return true;
            }
        }

        return false;
    }

    public function getMetaRobotsForHttps($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ROBOTS_FOR_HTTPS, $storeId);
    }

    /**
     *
     * @param int|null $storeId
     * @return bool|int
     */
    public function isUseNoindexByLimit($storeId = null)
    {
        return (bool)Mage::getStoreConfigFlag(self::XML_PATH_NOINDEX_BY_LIMIT, $storeId);
    }

    public function isReviewFriendlyUrlEnable($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REVIEW_FRIENDLY_URLS, $storeId);
    }

    public function isProductCanonicalUrlOnReviewPage()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_CANONICAL_FOR_REVIEW);
    }

    public function isProductPage($fullActionName)
    {
        $product = Mage::registry('current_product');
        if (is_object($product) && $product->getId()) {

            $productActions = array(
                'catalog_product_view',
                'review_product_list',
                'review_product_view',
                'productquestions_show_index',
            );

            if (in_array($fullActionName, $productActions)) {
                return true;
            }
        }
        return false;
    }

    public function isCategoryPage($fullActionName)
    {
        $category = Mage::registry('current_category');
        if (is_object($category) && $category->getId()) {

            $categoryActions = array(
                'catalog_category_view',
            );

            if (in_array($fullActionName, $categoryActions)) {
                return true;
            }
        }
        return false;
    }

    public function isHomePage($fullActionName)
    {
        return ('cms_index_index' == $fullActionName);
    }

    public function isIncludeLNFiltersToCanonicalUrlByConfig()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ALLOW_FILTERS_CANONICAL);
    }

    protected function _cmp($a, $b)
    {
        $a['position'] = (empty($a['position'])) ? 0 : $a['position'];
        $b['position'] = (empty($b['position'])) ? 0 : $b['position'];

        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? +1 : -1;
    }

    public function useSpecificPortInCanonical()
    {
        return Mage::getStoreConfigFlag('mageworx_seo/seobase/add_canonical_url_port');
    }

    public function isCanonicalUrlEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_URL_ENABLED, $storeId);
    }

    public function getProductCanonicalType($storeId = null)
    {
        if (!$this->useCategoriesPathInProductUrl($storeId)) {
            return MageWorx_SeoBase_Model_Canonical_Product::ROOT;
        }
        return Mage::getStoreConfig(self::XML_PATH_CANONICAL_URL_TYPE, $storeId);
    }

    public function useCategoriesPathInProductUrl($store = null)
    {
        return Mage::getStoreConfigFlag('catalog/seo/product_use_categories', $store);
    }

    public function getRssGenerator()
    {
        return 'MageWorx SEO Suite (http://www.mageworx.com/)';
    }

    public function getStatusLinkRel()
    {
        return (int) Mage::getStoreConfig('mageworx_seo/seobase/enable_link_rel');
    }

    public function getPagerUrlFormat()
    {
        if (Mage::helper('mageworx_seobase/layeredFilter')->isLNFriendlyUrlsEnabled()) {
            return Mage::helper('seofriendlyln/config')->getPagerUrlFormat();
        }
        return false;
    }

    /**
     * Retrive list of pages for noindex, follow robots
     *
     * @param int $storeId
     * @return array
     */
    public function getNoindexPages($storeId = null)
    {
        $pagesString = Mage::getStoreConfig(self::XML_PATH_NOINDEX_PAGES, $storeId);
        $arrayRaw    = array_map('trim', explode(',', $pagesString));

        return array_filter($arrayRaw);
    }

    /**
     * Retrive list of user pages for noindex, follow robots
     *
     * @param int $storeId
     * @return array
     */
    public function getNoindexUserPages($storeId = null)
    {
        $pagesString = Mage::getStoreConfig(self::XML_PATH_NOINDEX_USER_PAGES, $storeId);
        $pagesArray  = array_filter(preg_split('/\r?\n/', $pagesString));
        $pagesArray  = array_map('trim', $pagesArray);
        return array_filter($pagesArray);
    }

    /**
     * Retrive list of pages for noindex, nofollow robots
     *
     * @param int $storeId
     * @return array
     */
    public function getNoindexNofollowUserPages($storeId = null)
    {
        $pagesString = Mage::getStoreConfig(self::XML_PATH_NOINDEX_NOFOLLOW_USER_PAGES, $storeId);
        $pagesArray = array_filter(preg_split('/\r?\n/', $pagesString));
        $pagesArray = array_map('trim', $pagesArray);
        return array_filter($pagesArray);
    }

    public function isCompoundProductType($typeId)
    {
        switch ($typeId) {
            case (Mage_Catalog_Model_Product_Type::TYPE_BUNDLE):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_GROUPED):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE):
                $ret = false;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL):
                $ret = false;
                break;
            default:
                $ret = false;
        }

        return $ret;
    }

    public function getCurrentFullActionName()
    {
        return Mage::helper('mageworx_seoall/request')->getCurrentFullActionName();
    }

    public function isEnterpriseSince113()
    {
        if (is_null($this->_enterpriseSince113)) {
            $mage = new Mage();
            if (is_callable(array($mage, 'getEdition')) && Mage::getEdition() == Mage::EDITION_ENTERPRISE
                && version_compare(Mage::getVersion(), '1.13.0.0', '>=')) {
                $this->_enterpriseSince113 = true;
            }
            else {
                $this->_enterpriseSince113 = false;
            }
        }
        return $this->_enterpriseSince113;
    }

    public function isUseCanonicalUrlFor404Page($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_FOR_NOROUTE_PAGE, $storeId);
    }
}