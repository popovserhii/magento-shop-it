<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * XML config path cross linking enabled
     */
    const XML_PATH_ENABLED                      = 'mageworx_seo/seocrosslinks/enabled';

    /**
     * XML config path replacement count for product
     */
    const XML_PATH_REPLACEMENT_COUNT_PRODUCT    = 'mageworx_seo/seocrosslinks/replacement_count_product';

    /**
     * XML config path replacement count for category
     */
    const XML_PATH_REPLACEMENT_COUNT_CATEGORY   = 'mageworx_seo/seocrosslinks/replacement_count_category';

    /**
     * XML config path replacement count for CMS page
     */
    const XML_PATH_REPLACEMENT_COUNT_CMS_PAGE   = 'mageworx_seo/seocrosslinks/replacement_count_cms_page';

    /**
     * XML config path replacement count for AW blog page
     */
    const XML_PATH_REPLACEMENT_COUNT_BLOG       = 'mageworx_seo/seocrosslinks/replacement_count_blog_page';

    /**
     * XML config path replacement count for AW blog page
     */
    const XML_PATH_USE_NAME_FOR_TITLE           = 'mageworx_seo/seocrosslinks/use_name_for_title';

    /**
     * XML config path product attributes for replacing
     */
    const XML_PATH_PRODUCT_ATTRIBUTES           = 'mageworx_seo/seocrosslinks/product_attributes';

    /**
     * XML config path default reference
     */
    const XML_PATH_DEFAULT_REFERENCE            = 'mageworx_seo/seocrosslinks/default_reference';

    /**
     * XML config path default replacement count
     */
    const XML_PATH_DEFAULT_REPLACEMENT_COUNT    = 'mageworx_seo/seocrosslinks/default_replacement_count';

    /**
     * XML config path default priority
     */
    const XML_PATH_DEFAULT_PRIORITY             = 'mageworx_seo/seocrosslinks/default_priority';

    /**
     * XML config path default status
     */
    const XML_PATH_DEFAULT_STATUS               = 'mageworx_seo/seocrosslinks/default_status';

    /**
     * XML config path default destination
     */
    const XML_PATH_DEFAULT_DESTINATION          = 'mageworx_seo/seocrosslinks/default_destination';

    /**
     * XML config path enabled grid columns
     */
    const XML_PATH_DEFAULT_GRID_COLUMNS         = 'mageworx_seo/seocrosslinks/default_grid_columns';

    /**
     * List of default destinations
     *
     * @var array
     */
    protected $_destinationDefault = null;

    /**
     * List of enabled grid columns
     *
     * @var array
     */
    protected $_gridColumnsDefault = null;


    /**
     * Checks if cross linking is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool)Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId);
    }

    /**
     * Retrive max replacement for product page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForProductPage($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_REPLACEMENT_COUNT_PRODUCT, $storeId);
    }

    /**
     * Retrive max replacement for category page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForCategoryPage($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_REPLACEMENT_COUNT_CATEGORY, $storeId);
    }

    /**
     * Retrive max replacement for CMS page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForCmsPage($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_REPLACEMENT_COUNT_CMS_PAGE, $storeId);
    }

    /**
     * Retrive max replacement for AW blog page
     *
     * @param int|null $storeId
     * @return int
     */
    public function getReplacemenetCountForBlogPage($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_REPLACEMENT_COUNT_BLOG, $storeId);
    }

    /**
     * Check if use product or category name for crosslink title
     */
    public function isUseNameForTitle($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_USE_NAME_FOR_TITLE, $storeId);
    }

    /**
     * Retrive list of product attributes for replace
     *
     * @param int|null $storeId
     * @return array
     */
    public function getProductAttributesForReplace($storeId = null)
    {
        $productAttributesAsString = Mage::getStoreConfig(self::XML_PATH_PRODUCT_ATTRIBUTES, $storeId);
        return array_filter(array_map('trim', explode(',', $productAttributesAsString)));
    }

    /**
     * Retrive default reference
     *
     * @return string
     */
    public function getDefaultReference()
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_REFERENCE);
    }

    /**
     * Retrive default replacement count
     *
     * @return int
     */
    public function getDefaultReplacementCount()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_DEFAULT_REPLACEMENT_COUNT);
    }

    /**
     * Retrive default priority
     *
     * @return int
     */
    public function getDefaultPriority()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_DEFAULT_PRIORITY);
    }

    /**
     * Retrive default status
     *
     * @return int
     */
    public function getDefaultStatus()
    {
        return (int)Mage::getStoreConfigFlag(self::XML_PATH_DEFAULT_STATUS);
    }

    /**
     * Retrive default list of destinations
     *
     * @return array
     */
    public function getDefaultDestinationArray()
    {
        if (is_null($this->_destinationDefault)) {
            $arrayRaw = array_map('trim', explode(',', Mage::getStoreConfig(self::XML_PATH_DEFAULT_DESTINATION)));
            $this->_destinationDefault = array_filter($arrayRaw);
        }
        return $this->_destinationDefault;
    }

    /**
     * Check if destination for product enabled by default
     *
     * @return bool
     */
    public function getDefaultForProductPage()
    {
        return in_array('product_page', $this->getDefaultDestinationArray());
    }

    /**
     * Check if destination for category enabled by default
     *
     * @return bool
     */
    public function getDefaultForCategoryPage()
    {
        return in_array('category_page', $this->getDefaultDestinationArray());
    }

    /**
     * Check if destination for CMS page enabled by default
     *
     * @return bool
     */
    public function getDefaultForCmsPageContent()
    {
        return in_array('cms_page_content', $this->getDefaultDestinationArray());
    }

    /**
     * Check if destination for blog enabled by default
     *
     * @return bool
     */
    public function getDefaultForBlogContent()
    {
        return in_array('blog_content', $this->getDefaultDestinationArray());
    }

    /**
     * Retrive list of grid columns enabled by default
     *
     * @return array
     */
    public function getDefaultGridColumArray()
    {
        if (is_null($this->_gridColumnsDefault)) {
            $arrayRaw = array_map('trim', explode(',', Mage::getStoreConfig(self::XML_PATH_DEFAULT_GRID_COLUMNS)));
            $this->_gridColumnsDefault = array_filter($arrayRaw);
        }
        return $this->_gridColumnsDefault;
    }

    /**
     * Check if link grid column enabled by default
     *
     * @return bool
     */
    public function showLinkTitleColumn()
    {
        return in_array('link_title', $this->getDefaultGridColumArray());
    }

    /**
     * Check if link target column enabled by default
     *
     * @return bool
     */
    public function showLinkTargetColumn()
    {
        return in_array('link_target', $this->getDefaultGridColumArray());
    }

    /**
     * Check if store view column enabled by default
     *
     * @return bool
     */
    public function showStoreViewColumn()
    {
        return in_array('store_id', $this->getDefaultGridColumArray());
    }

    /**
     * Check if URL column enabled by default
     *
     * @return bool
     */
    public function showStaticUrlColumn()
    {
        return in_array('ref_static_url', $this->getDefaultGridColumArray());
    }

    /**
     * Check if reference by product SKU column enabled by default
     *
     * @return bool
     */
    public function showProductBySkuColumn()
    {
        return in_array('ref_product_sku', $this->getDefaultGridColumArray());
    }

    /**
     * Check if reference by category ID grid column enabled by default
     *
     * @return bool
     */
    public function showCategoryByIdColumn()
    {
        return in_array('ref_category_id', $this->getDefaultGridColumArray());
    }

    /**
     * Check if replacement count grid column enabled by default
     *
     * @return bool
     */
    public function showReplacementCountColumn()
    {
        return in_array('replacement_count', $this->getDefaultGridColumArray());
    }

    /**
     * Check if show in product page grid column enabled by default
     *
     * @return bool
     */
    public function showInProductColumn()
    {
        return in_array('in_product', $this->getDefaultGridColumArray());
    }

    /**
     * Check if show in category page grid column enabled by default
     *
     * @return bool
     */
    public function showInCategoryColumn()
    {
        return in_array('in_category', $this->getDefaultGridColumArray());
    }

    /**
     * Check if show in CMS page grid column enabled by default
     *
     * @return bool
     */
    public function showInCmsPageColumn()
    {
        return in_array('in_cms_page', $this->getDefaultGridColumArray());
    }

    /**
     * Check if show in blog page grid column enabled by default
     *
     * @return bool
     */
    public function showInBlogColumn()
    {
        return in_array('in_blog', $this->getDefaultGridColumArray());
    }
}