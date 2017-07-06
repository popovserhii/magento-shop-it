<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 Mageparam typeWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Canonical_Category extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    /**
     *
     * @var MageWorx_SeoBase_Helper_LayeredFilter
     */
    protected $_helperLF;

    /**
     *
     * @var string
     */
    protected $_entityType = 'category';

    /**
     *
     * @param Mage_Catalog_Model_Category|null $item
     * @param int|null $storeId
     * @return string
     */
    protected function _getCanonicalUrl($item = null, $storeId = null)
    {
        $this->_helperLF = Mage::helper('mageworx_seobase/layeredFilter');

        $category = $item ? $item : Mage::registry('current_category');

        if (!is_object($category)) {
            return '';
        }

        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $toolbar    = Mage::app()->getLayout()->getBlock('product_list_toolbar');

        if (Mage::helper('mageworx_seoall/layeredFilter')->isApplyedLayeredNavigationFilters()) {
            if ($this->_helperData->isIncludeLNFiltersToCanonicalUrlByConfig() ==
                MageWorx_SeoBase_Helper_LayeredFilter::CATEGORY_LN_CANONICAL_OFF
            ) {
                return '';
            }
            $url = $this->_getCanonicalUrlForCategoryLNPage($category, $currentUrl, $toolbar);
        }
        else {
            $url = $this->_getCanonicalUrlForCategoryPage($category, $toolbar);

            $crossDomainStoreId = $this->_getCrossDomainStoreId();
            if ($url & $crossDomainStoreId) {
                $url = $this->_getUrlRewriteCanonical($category, $crossDomainStoreId, $url);
            }
        }

        if (!$this->_helperData->isUsePagerInCanonical()) {
            $url = $this->_deletePagerParameter($url, $toolbar);
        }

        return $url ? $this->renderUrl($url) : $this->renderUrl($category->getUrl());
    }

    /**
     *
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Catalog_Block_Product_List_Toolbar $toolbar
     * @return string
     */
    protected function _getCanonicalUrlForCategoryPage($category, $toolbar)
    {
        /// Magento bug? For category with display mode = PAGE,
        /// If clear LN filters the pager will remain in the category URL
        if ($category->getDisplayMode() == 'PAGE') {
            return $category->getUrl();
        }

        if (!$this->_helperData->isUseLimitAll() && !$this->_helperData->isUsePagerInCanonical()) {
            return $category->getUrl();
        }

        $availableLimits = $this->_getAvailableLimits($toolbar);

        ///CATEGORY URLS WITH PAGE ALL
        if (!empty($availableLimits['all'])) {
            $url = $category->getUrl();
            $url = $this->_addLimitAllToUrl($url, $toolbar);
        }

        ///CATEGORY URLS WITHOUT PAGE ALL
        else {
            $url = $this->_changePagerParameterToCurrentForCurrentUrl();
            $url = $this->_cropDefaultLimit($url, $toolbar);
            $url = $this->_deleteSortParameters($url, $toolbar);
        }

        return $url;
    }

    /**
     *
     * @param Mage_Catalog_Model_Category $category
     * @param string $currentUrl
     * @param Mage_Catalog_Block_Product_List_Toolbar $toolbar
     * @return string
     */
    protected function _getCanonicalUrlForCategoryLNPage($category, $currentUrl, $toolbar)
    {
        $availableLimits = $this->_getAvailableLimits($toolbar);

        ///FRIENDLY LN URLS
        if ($this->_helperLF->isLNFriendlyUrlsEnabled()) {
            ///FRIENDLY LN URLS WITH PAGE ALL

            if (!empty($availableLimits['all'])) {
                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {
                    $url = $this->_deleteSortParameters($currentUrl, $toolbar);
                    $url = $this->_deleteLimitParameter($url, $toolbar);
                    $url = $this->_deletePagerParameter($url, $toolbar);
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
                else {
                    $url = $category->getUrl();
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
            }
            ///FRIENDLY LN URLS WITHOUT PAGE ALL
            else {
                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {

                    if ($this->_helperData->isUsePagerInCanonical()) {
                        $url = $this->_changePagerParameterToCurrentForCurrentUrl();
                    } else {
                        $url = $this->_deletePagerParameter(Mage::helper('core/url')->getCurrentUrl(), $toolbar);
                        $url = $this->_deleteLimitParameter($url, $toolbar);
                    }

                    $url = $this->_cropDefaultLimit($url, $toolbar);
                    $url = $this->_deleteSortParameters($url, $toolbar);
                }
                else {
                    //Maybe better without canonical url...?
                    $url = $category->getUrl();
                }
            }
        }
        ///DEFAULT LN URLS
        else {

            $subCategory = $this->_getSubCategoryForCanonical($currentUrl);

            if (is_object($subCategory)) {
                $subCategoryUrl = $this->_convertSubCategoryUrl($currentUrl, $subCategory);
                if ($subCategory->getDisplayMode() == 'PAGE') {
                    return $subCategoryUrl;
                }
            }

            ///DEFAULT LN URLS WITH PAGE ALL
            if (!empty($availableLimits['all'])) {

                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {
                    $url = $this->_deleteSortParameters($currentUrl, $toolbar);
                    $url = $this->_deleteLimitParameter($url, $toolbar);
                    $url = $this->_deletePagerParameter($url, $toolbar);
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
                else {
                    $url = $category->getUrl();
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
            }
            ///DEFAULT LN URLS WITHOUT PAGE ALL
            else {
                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {
                    $url = $this->_deleteSortParameters($currentUrl, $toolbar);

                    if (!$this->_helperData->isUsePagerInCanonical()) {
                        $url = $this->_deletePagerParameter($url, $toolbar);
                        $url = $this->_deleteLimitParameter($url, $toolbar);
                    }
                }
                else {
                    //Maybe without canonical url better...?
                    $url = $category->getUrl();
                }
            }
        }

        return !empty($url) ? $url : '';
    }

    /**
     *
     * @param Mage_Catalog_Block_Product_List_Toolbar $toolbar
     * @return array
     */
    protected function _getAvailableLimits($toolbar)
    {
        $availableLimit = array();

        if (is_object($toolbar) && ($toolbar instanceof Mage_Catalog_Block_Product_List_Toolbar)) {
            $availableLimit = $toolbar->getAvailableLimit();

            if (!Mage::helper('mageworx_seobase')->isUseLimitAll() && !empty($availableLimit['all'])) {
                unset($availableLimit['all']);
            }
        }

        return $availableLimit;
    }

    /**
     *
     * @param string $url
     * @return Mage_Catalog_Model_Category
     */
    protected function _getSubCategoryForCanonical($url)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }

        parse_str(html_entity_decode($parseUrl['query']), $params);
        if (!empty($params['cat']) && is_numeric($params['cat'])) {
            $catId       = $params['cat'];
            $subCategory = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($catId);
        }
        return (!empty($subCategory)) ? $subCategory : false;
    }

    /**
     *
     * @param string $url
     * @param Mage_Catalog_Block_Product_List_Toolbar $toolbar
     * @return string
     */
    protected function _cropDefaultLimit($url, $toolbar)
    {
        if (is_callable(array($toolbar, 'getDefaultPerPageValue')) && is_callable(array($toolbar, 'getLimit'))) {
            if ($toolbar->getDefaultPerPageValue() == $toolbar->getLimit()) {
                $url = $this->_deleteLimitParameter($url, $toolbar);
            }
        }
        return $url;
    }

    /**
     * Retrive current URL with a specified pager: with parameter 'p =' or as URL part: '* [page_number] * '.html? =...
     * Example 1:
     *      Old url from google search: example.com/computers?p=2
     *      Retrive url: example.com/computers-page2.html (If friendly pager ON, etc.)
     * Example 2 (with layered, sort and mode params):
     *      Old url from google search: example.com/electronics/lnav/price:0-1000.html?p=3&limit=15&mode=list
     *      Retrive url:                example.com/electronics/lnav/price:0-1000-page3.html?limit=15&mode=list
     * @return string
     */
    protected function _changePagerParameterToCurrentForCurrentUrl()
    {
        $pageNum = Mage::helper('mageworx_seoall/url')->getPageNumFromUrl();

        $pager   = Mage::app()->getLayout()->getBlock('product_list_toolbar_pager');

        //If friendly url disable
        //Canonical for ex.com/computers.html?p=1 is ex.com/computers.html?p=1,
        //Canonical for ex.com/computers.html     is ex.com/computers.html
        //If friendly url enable and use custom pager
        //Canonical for ex.com/computers.html     is ex.com/computers.html
        //Canonical for ex.com/computers.html?p=1 is ex.com/computers.html
        //Because for custom pager url we don't use '1'

        if (is_object($pager)) {
            if (!$pageNum) {
                return Mage::helper('core/url')->getCurrentUrl();
            }
            elseif ($pageNum == 1 && $this->_helperLF->isLNFriendlyUrlsEnabled() && $this->_helperData->getPagerUrlFormat()) {
                return $this->_deletePagerParameter(Mage::helper('core/url')->getCurrentUrl(),
                        Mage::app()->getLayout()->getBlock('product_list_toolbar'));
            }
            elseif ($pageNum == 1) {
                return $this->_deletePagerParameter(Mage::helper('core/url')->getCurrentUrl(),
                    Mage::app()->getLayout()->getBlock('product_list_toolbar'));
            }
            else {
                return $pager->getPageUrl($pageNum);
            }
        }

        return Mage::helper('core/url')->getCurrentUrl();
    }

    /**
     *
     * @param string $url
     * @param Mage_Catalog_Block_Product_List_Toolbar $toolbar
     * @return string
     */
    public function _deletePagerParameter($url, $toolbar)
    {
        $pagerFormat = $this->_helperData->getPagerUrlFormat();
        if ($pagerFormat) {
            $pattern         = '#' . str_replace('[page_number]', '[0-9]+', $pagerFormat) . '#';
            $urlWithoutPager = preg_replace($pattern, '', $url);
            $url             = (is_null($urlWithoutPager)) ? $url : $urlWithoutPager;
            return $url;
        }

        return parent::_deletePagerParameter($url, $toolbar);
    }

    /**
     *
     * @param string $url
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    protected function _convertSubCategoryUrl($url, $category)
    {
        $parseUrl    = parse_url($url);
        $categoryUrl = $category->getUrl();

        if (!empty($categoryUrl)) {
            $url = $categoryUrl . '?' . $parseUrl['query'];
            $url = $this->deleteParametrsFromUrl($url, array('cat'));
        }

        return $url;
    }

    /**
     *
     * @param string $url
     * @param array $cropParams
     * @return string
     */
    public function deleteParametrsFromUrl($url, array $cropParams)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }

        parse_str(html_entity_decode($parseUrl['query']), $params);

        foreach ($cropParams as $cropName) {
            if (array_key_exists($cropName, $params)) {
                unset($params[$cropName]);
            }
        }

        $queryString = '';
        foreach ($params as $name => $value) {
            if ($queryString == '') {
                $queryString = '?' . $name . '=' . $value;
            }
            else {
                $queryString .= '&' . $name . '=' . $value;
            }
        }

        $url = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'] . $queryString;
        return $url;
    }

    /**
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int $storeId
     * @param string $url
     * @return string
     */
    protected function _getUrlRewriteCanonical($category, $storeId, $url)
    {
        if (!is_object($category)) {
            return '';
        }

        $canonicalUrl  = '';

        $collection = Mage::getResourceModel('mageworx_seobase/core_url_rewrite_collection')
            ->addStoreFilter($storeId, false);

        $collection->getSelect()->where('category_id = ?', (int)$category->getId());
        $collection->getSelect()->where('is_system = 1');

        $urlRewrite = $collection->getFirstItem();

        if ($urlRewrite && $urlRewrite->getRequestPath()) {
            $canonicalUrl = $this->_helperStore->getStoreBaseUrl($storeId) . $urlRewrite->getRequestPath();

            if(strpos($url, '?') !== false) {
                $params = substr($url, strpos($url, '?'));
            }

            if (!empty($params)) {
                $canonicalUrl .= $params;
            }
        }

        return $canonicalUrl;
    }
}