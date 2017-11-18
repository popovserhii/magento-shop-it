<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Model_Generator
{
    const XML_PATH_HOME_PAGE    = 'web/default/cms_home_page';

    /**
     * @var MageWorx_XSitemap_Model_Sitemap
     */
    protected $_model;

    /**
     * @var MageWorx_XSitemap_Helper_Data
     */
    protected $_helper;

    /**
     * @var MageWorx_SeoAll_Helper_TrailingSlash
     */
    protected $_helperTrailingSlash;

    /**
     * @var MageWorx_XSitemap_Model_Writer
     */
    protected $_xmlWriter;
    protected $_entityName;
    protected $_storeBaseUrl;
    protected $_storeId;
    protected $_counter      = 0;
    protected $_totalProduct = 0;


    protected function _init(MageWorx_XSitemap_Model_Sitemap $model, $entityName)
    {
        $this->_entityName          = $entityName;
        $this->_model               = $model;
        $this->_storeId             = $model->getStoreId();
        $this->_helper              = Mage::helper('xsitemap');
        $this->_helper->init($this->_storeId);
        $this->_storeBaseUrl        = $this->_getStoreBaseUrl();
        $this->_storeBaseUrlTypeWeb = $this->_getStoreBaseUrlTypeWeb();
        $this->_helperTrailingSlash = Mage::helper('mageworx_seoall/trailingSlash');
        $this->_initWriter($entityName);
    }

    protected function _initWriter($entityName)
    {
        $this->_xmlWriter = Mage::getModel('xsitemap/writer');
        $this->_xmlWriter->init($this->_model->getFullPath(), $this->_model->getSitemapFilename(),
            $this->_model->getFullTempPath(), $this->_isFirstStepGeneration($entityName),
            $this->_isEndStepGeneration($entityName), $this->_getStoreBaseUrlForSitemapIndex()
        );
    }

    protected function _isFirstStepGeneration($entityName)
    {
        // category - first entity name in entity name list when step by step generate xml (from GUI)
        return (!$entityName || $entityName == 'category') ? true : false;
    }

    protected function _isEndStepGeneration($entityName)
    {
        return (!$entityName || $entityName == 'sitemap_finish') ? true : false;
    }

    protected function _getTempPath()
    {

    }

    public function generateXml(MageWorx_XSitemap_Model_Sitemap $model, $entityName = false)
    {
        $this->_init($model, $entityName);

        if (!$this->_entityName || $this->_entityName == 'category') {
            $this->_generateXmlFromCategories();
        }

        if (!$this->_entityName || $this->_entityName == 'product') {
            $this->_generateXmlFromProducts();
        }

        if (!$this->_entityName || $this->_entityName == 'tag') {
            $this->_generateXmlFromProductTags();
        }

        if (!$this->_entityName || $this->_entityName == 'cms') {
            $this->_generateXmlFromCms();
        }

        if (!$this->_entityName || $this->_entityName == 'blog') {

            if ((string) Mage::getConfig()->getModuleConfig('AW_Blog')->active == 'true') {
                $this->_generateAwBlog();
            }

            if ((string) Mage::getConfig()->getModuleConfig('Fishpig_Wordpress')->active == 'true') {
                $this->_generateFishpigBlog();
            }
        }

        if (!$this->_entityName || $this->_entityName == 'additional_links') {
            $this->_generateFromAdditionalLinks();
        }

        if (!$this->_entityName || $this->_entityName == 'fishpig_attribute_splash_pages') {
            if ((string) Mage::getConfig()->getModuleConfig('Fishpig_AttributeSplash')->active == 'true') {
                $this->_generateFromFishpigAttributeSplashPages();
            }
        }

        if (!$this->_entityName || $this->_entityName == 'fishpig_attribute_splash_pro_pages') {
            if ((string) Mage::getConfig()->getModuleConfig('Fishpig_AttributeSplashPro')->active == 'true') {
                $this->_generateFromFishpigAttributeSplashProPages();
            }
        }

        if (!$this->_entityName || $this->_entityName == 'sitemap_finish') {
            //$this->_xmlWriter->closeXml();
            unset($this->_xmlWriter);
        }
    }

    protected function _generateFromFishpigAttributeSplashProPages()
    {
        if ($this->_helper->isFishpigAttributeSplashProGenerateEnabled()) {
            return;
        }

        $changefreq     = $this->_helper->getFishpigAttributSplashProChangeFrequency();
        $priority       = $this->_helper->getFishpigAttributSplashProPriority();
        $splashProPages = $this->_helper->getFishpigAttributSplashProPages();

        if (!count($splashProPages) > 0) {
            return;
        }

        foreach ($splashProPages as $page) {
            $url = substr($page->getUrl(), strpos($page->getUrl(), $page->getUrlKey()));
            $url = $this->getStoreItemUrl($url);
            $url = $this->_helperTrailingSlash->trailingSlash('splashProPage', $url);

            $lastmode = $this->_helper->getFishpigAttributSplashProLastModifiedDate($page);
            $this->_xmlWriter->write($url, $lastmode, $changefreq, $priority);
        }
    }

    protected function _generateFromFishpigAttributeSplashPages()
    {
        if (!$this->_helper->isFishpigAttributeSplashGenerateEnabled()) {
            return;
        }

        $changefreq_pages = $this->_helper->getFishpigAttributeSplashPageFrequency();
        $priority_pages   = $this->_helper->getFishpigAttributeSplashPagePriority();
        $changefreq_group = $this->_helper->getFishpigAttributeSplashGroupFrequency();
        $priority_group   = $this->_helper->getFishpigAttributeSplashGroupPriority();

        $splashPages = $this->_helper->getFishpigAttributSplashPages();

        if (count($splashPages) > 0) {
            foreach ($splashPages as $page) {
                $page->setStoreId($this->_storeId);
                $url = $this->_helperTrailingSlash->trailingSlash('splashPage', $page->getUrl());
                $lastmode = $page->getUpdatedAt(false) ? $page->getUpdatedAt(false) : $this->_getDate();
                $this->_xmlWriter->write($url, $lastmode, $changefreq_pages,
                    $priority_pages);
            }
        }


        if ($this->_helper->isFishpigAttributeSplashGroupPagesEnabled()) {
            $splashGroups = $this->_helper->getFishpigAttributSplashGroups();

            if (!count($splashGroups)) {
                return;
            }

            foreach ($splashGroups as $group) {

                if (!$group->canDisplay()) {
                    continue;
                }

                if (Mage::app()->isSingleStoreMode() || $group->getStoreId() == $this->_storeId) {
                    $lastmode = $group->getUpdatedAt(false) ? $group->getUpdatedAt(false) : $this->_getDate();
                    $url      = $this->_helperTrailingSlash->trailingSlash('splashGroup', $group->getUrl());
                    $this->_xmlWriter->write($url, $lastmode, $changefreq_group, $priority_group);
                }
            }
        }
    }

    protected function _generateFishpigBlog()
    {
        if ($this->_helper->isFishpigBlogEnabled() && $this->_helper->isBlogGenerateEnabled()) {
            $initialEnvironmentInfo = Mage::getSingleton('core/app_emulation')->startEnvironmentEmulation($this->_storeId);
            $this->_xmlWriter->write(Mage::helper('wordpress')->getUrl(), $this->_getDate(), 'daily', '1.0');

            $posts = Mage::getResourceModel('wordpress/post_collection')
                ->addIsViewableFilter()
                ->setOrderByPostDate()
                ->load();

            $changefreq = $this->_helper->getBlogChangeFrequency();
            $priority   = $this->_helper->getBlogPriority();

            foreach($posts as $post) {
                $lastmode = $post->getPostModifiedDate('Y-m-d') ? $post->getPostModifiedDate('Y-m-d') : $this->_getDate();
                $this->_xmlWriter->write($post->getUrl(), $lastmode, $changefreq, $priority);
            }

            $categories = Mage::getResourceModel('wordpress/term_collection')
                ->addTaxonomyFilter('category')
                ->addParentIdFilter(0)
                ->addHasObjectsFilter();

            foreach($categories as $category) {
                $this->_xmlWriter->write($category->getUrl(), $this->_getDate(), $changefreq, $priority);
            }
            Mage::getSingleton('core/app_emulation')->stopEnvironmentEmulation($initialEnvironmentInfo);
        }
    }

    protected function _generateAwBlog()
    {
        if ($this->_helper->isAwBlogEnabled() && $this->_helper->isBlogGenerateEnabled()) {
            $defaultRote = (string) Mage::getStoreConfig('blog/blog/route', $this->_storeId);
            if (!$defaultRote) {
                $defaultRote = 'blog';
            }
            $changefreq = $this->_helper->getBlogChangeFrequency();
            $priority   = $this->_helper->getBlogPriority();
            $collection = Mage::getResourceModel('xsitemap/blog_page')->getCollection($this->_storeId);
            foreach ($collection as $item) {
                list($lastmode) = explode(' ', $item->getDate());
                if (!$lastmode){
                    $lastmode = $this->_getDate();
                }
                $url = $this->_storeBaseUrl . $defaultRote . "/" . $item->getUrl();
                $url = $this->_helperTrailingSlash->trailingSlash('blog', $url);
                $this->_xmlWriter->write($url, $lastmode, $changefreq, $priority);
            }
            unset($collection);
        }
    }

    protected function _generateFromAdditionalLinks()
    {
        $changefreq = $this->_helper->getLinkChangeFrequency();
        $priority   = $this->_helper->getLinkPriority();
        $addLinks   = array_filter(preg_split('/\r?\n/',
                $this->_helper->getAdditionalLinksForXmlSitemap($this->_storeId)));

        if (count($addLinks)) {
            foreach ($addLinks as $link) {
                if (strpos($link, ',') !== false) {
                    list($link) = explode(',', $link);
                }
                $link = trim($link);
                if (strpos($link, 'http') !== false) {
                    $links[] = new Varien_Object(array('url' => $link));
                }
                else {
                    list($url) = explode("/?",
                        Mage::getModel('core/store')->load($this->_storeId)->getUrl((string) $link));
                    $links[] = new Varien_Object(array('url' => $url));
                }
            }
        }

        if (!empty($links) && count($links)) {
            foreach ($links as $item) {
                $url     = $this->_helperTrailingSlash->trailingSlash('link', $item->getUrl());
                $lastmod = $this->_getDate();
                $this->_xmlWriter->write($url, $lastmod, $changefreq, $priority);
            }
            unset($links);
        }
    }

    protected function _generateXmlFromCategories()
    {
        $changefreq = $this->_helper->getCategoryChangeFrequency();
        $priority   = $this->_helper->getCategoryPriority();

        $collection = Mage::helper('xsitemap/factory')->getCategoryXmlResource()->getCollection($this->_storeId);
        $altCodes   = Mage::helper('xsitemap')->getAlternateFinalCodes('category', $this->_storeId);

        if(!empty($altCodes)){
            $alternateResource       = Mage::helper('mageworx_seobase/factory')->getCategoryAlternateUrlResource();
            $alternateUrlsCollection = $alternateResource->getAllCategoryUrls(array_keys($altCodes));
        }

        foreach ($collection as $item) {
            $model   = Mage::getModel('catalog/category')->load($item->getId());
            $url     = $this->_storeBaseUrl . $item->getUrl();
            $lastmod = $this->_getItemChangeDate($model);

            if(!empty($alternateUrlsCollection[$item->getId()])){
                $storeUrls = $alternateUrlsCollection[$item->getId()]['alternateUrls'];
                $alternateUrls = array();
                foreach($storeUrls as $storeId => $altUrl){
                    if(!empty($altCodes[$storeId])){
                        $alternateUrls[$altCodes[$storeId]] = $altUrl;
                    }
                }
            }

            $alternateUrls = !empty($alternateUrls) ? array_unique($alternateUrls) : array();

            $this->_xmlWriter->write(
                $this->_helperTrailingSlash->trailingSlash('category', $url),
                $lastmod,
                $changefreq,
                $priority,
                false,
                $alternateUrls
            );
        }

        unset($collection);
    }

    public function getProductImageUrl($imageFile)
    {
        return $this->_getStoreBaseUrlTypeWeb() . 'media/catalog/product' . $imageFile;
    }

    protected function _generateXmlFromProducts()
    {
        $this->_totalProduct = Mage::helper('xsitemap/factory')->getProductXmlResource()->getCollection($this->_storeId, true);
        $isProductImages     = $this->_helper->isProductImages();
        $changefreq          = $this->_helper->getProductChangeFrequency();
        $priority            = $this->_helper->getProductPriority();
        $limit               = $this->_helper->getXmlItemsLimit();

        if ($this->_entityName == "") {
            $limit = $this->_totalProduct;
        }

        if ($this->_counter < $this->_totalProduct) {

            if ($this->_counter + $limit > $this->_totalProduct) {
                $limit = $this->_totalProduct - $this->_counter;
            }

            $collection = Mage::helper('xsitemap/factory')->getProductXmlResource()->getCollection($this->_storeId, false, $limit, $this->_counter);
            $this->_counter += $limit;

            $altCodes = Mage::helper('xsitemap')->getAlternateFinalCodes('product', $this->_storeId);

            if(!empty($altCodes)){
                $arrayTargetPath = array();
                foreach ($collection as $val) {
                    $arrayTargetPath[$val->getId()] = $val->getTargetPath();
                }

                $alternateResource       = Mage::helper('mageworx_seobase/factory')->getProductAlternateUrlResource();
                $alternateUrlsCollection = $alternateResource->getAllProductUrls(array_keys($altCodes), $arrayTargetPath);
            }

            if (Mage::helper('xsitemap')->isUseImageCache()) {
                $appEmulation = Mage::getSingleton('core/app_emulation');
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->_storeId);
            }

            foreach ($collection as $item) {
                //Custom canonical URL can content 'http[s]://'
                if(strpos(trim($item->getUrl()), 'http') === 0){
                    $url = $item->getUrl();
                }else{
                    $url = $this->_storeBaseUrl . $item->getUrl();
                }
                $lastmod = $this->_getItemChangeDate($item);

                if ($isProductImages) {

                    if (Mage::helper('xsitemap')->isUseImageCache()) {
                        $image = $item->getImage();
                        $imageUrl = array();
                        $imageUrl[] = Mage::helper('xsitemap/catalog_image')->initialize($item, 'image', $image);
                    } else {
                        $imageUrl = array();
                        $gallery  = $item->getGallery();
                        if (is_array($gallery) && $gallery) {

                            foreach ($gallery as $image) {

                                if ($image['file'] == $item->getImage()) {
                                    $imageUrl = array($this->getProductImageUrl($image['file']));
                                    break;
                                }

                                $imageUrl[] = $this->getProductImageUrl($image['file']);
                            }
                            $imageUrl = array($imageUrl[0]);
                        }
                    }
                }

                if(empty($imageUrl)){
                    $imageUrl = false;
                }

                if(!empty($alternateUrlsCollection[$item->getId()])){
                    $storeUrls = $alternateUrlsCollection[$item->getId()]['alternateUrls'];
                    $alternateUrls = array();
                    foreach($storeUrls as $storeId => $altUrl){
                        if(!empty($altCodes[$storeId])){
                            $alternateUrls[$altCodes[$storeId]] = $altUrl;
                        }
                    }
                }

                $alternateUrls = !empty($alternateUrls) ? array_unique($alternateUrls) : array();

                $this->_xmlWriter->write(
                    $this->_helperTrailingSlash->trailingSlash('product', $url),
                    $lastmod,
                    $changefreq,
                    $priority,
                    $imageUrl,
                    $alternateUrls
                );
            }

            if (Mage::helper('xsitemap')->isUseImageCache()) {
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            }

            unset($collection);
        }
    }

    protected function _getItemChangeDate($model)
    {
        $upTime = $model->getUpdatedAt();
        if ($upTime == '0000-00-00 00:00:00') {
            $upTime = $model->getCreatedAt();
        }
        return substr($upTime, 0, 10);
    }

    protected function getStoreItemUrl($url)
    {
        return $this->_getStoreBaseUrl() . ltrim($url, '/');
    }

    protected function _getStoreBaseUrlForSitemapIndex()
    {
        return $this->_getStoreBaseUrlTypeWeb() . ltrim($this->_model->getSitemapPath(), '/');
    }

    protected function _getStoreBaseUrl()
    {
        $url = Mage::app()->getStore($this->_storeId)->getUrl();
        $cropUrl = (strpos($url, "?")) ? substr($url, 0, strpos($url, "?")) : $url;
        return rtrim($cropUrl, '/') . '/';
    }

    protected function _getStoreBaseUrlTypeWeb()
    {
        $url = Mage::getModel('core/store')->load($this->_storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $cropUrl = (strpos($url, "?")) ? substr($url, 0, strpos($url, "?")) : $url;
        return rtrim($cropUrl, '/') . '/';
    }

    protected function _getDate()
    {
        return Mage::getSingleton('core/date')->gmtDate('Y-m-d');
    }

    protected function _generateXmlFromProductTags()
    {
        if ($this->_helper->isProductTagsGenerateEnabled()) {
            $changefreq = $this->_helper->getProductTagsChangeFrequency();
            $priority   = $this->_helper->getProductTagsPriority();
            $collection = Mage::getModel('tag/tag')->getPopularCollection()
                ->joinFields($this->_storeId)
                ->load();

            foreach ($collection as $item) {
                $url = str_replace($this->_storeBaseUrl . 'index.php/', $this->_storeBaseUrl,
                    $item->getTaggedProductsUrl());

                if ($start = strpos($url, 'tag/')) {
                    $url = substr_replace($url, $this->_storeBaseUrl, 0, $start);
                }

                $url     = $this->_helperTrailingSlash->trailingSlash('tag', $url);
                $lastmod = $this->_getDate();

                $this->_xmlWriter->write($url, $lastmod, $changefreq, $priority);
            }
            unset($collection);
        }
    }

    protected function _generateXmlFromCms()
    {
        $changefreq = $this->_helper->getPageChangeFrequency();
        $collection = Mage::getResourceModel('xsitemap/cms_page')->getCollection($this->_storeId);

        $altCodes = Mage::helper('xsitemap')->getAlternateFinalCodes('cms', $this->_storeId);

        foreach ($collection as $item) {
            $isHomePage = false;

            $alternateUrls = array();

            if (Mage::helper('xsitemap')->isHomePage($item->getUrl())) {
                $isHomePage = true;
                $item->setUrl('');
                $priority = 1;
            }else{
            	$priority = $this->_helper->getPagePriority();
            }

            if(!empty($altCodes)){
                $alternateUrlsCollection = Mage::getResourceModel('mageworx_seobase/hreflang_cms_page')
                    ->getAllCmsUrls(array_keys($altCodes), $this->_storeId, $item->getId(), $isHomePage);

                if(empty($alternateUrlsCollection)){
                    continue;
                }

                if($isHomePage){
                    $alternateUrlsCollection = array_shift($alternateUrlsCollection);
                    $storeUrls = $storeUrls = $alternateUrlsCollection['alternateUrls'];

                    foreach($storeUrls as $storeId => $altUrl){
                        if(!empty($altCodes[$storeId])){
                            $altUrl = $this->_helperTrailingSlash->trailingSlash('home', $altUrl, $storeId);
                            $alternateUrls[$altCodes[$storeId]] = $altUrl;
                        }
                    }
                }else{
                    $storeUrls = $alternateUrlsCollection[$item->getId()]['alternateUrls'];

                    foreach($storeUrls as $storeId => $altUrl){
                        if(!empty($altCodes[$storeId])){
                            $alternateUrls[$altCodes[$storeId]] = $altUrl;
                        }
                    }
                }
            }

            if ($isHomePage) {
                $url = $this->_helperTrailingSlash->trailingSlash('home', $this->_storeBaseUrl);
            } else {
                $url = $this->_storeBaseUrl . $item->getUrl();
            }
            $lastmod = $this->_getDate();
            $this->_xmlWriter->write(
                $this->_helperTrailingSlash->trailingSlash('page', $url),
                $lastmod,
                $changefreq,
                $priority,
                false,
                array_unique($alternateUrls)
            );
        }
        unset($collection);
    }

    public function setCounter($num)
    {
        $this->_counter = $num;
    }

    public function getCounter()
    {
        return $this->_counter;
    }

    public function getTotalProduct()
    {
        return $this->_totalProduct;
    }

}