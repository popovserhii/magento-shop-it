<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    protected $_request = null;
    protected $_urlData = null;

    public function initControllerRouters($observer)
    {
        $front  = $observer->getEvent()->getFront();
        //  Varien_Autoload::registerScope('catalog');
        $router = new MageWorx_SeoFriendlyLN_Controller_Router();
        $front->addRouter('seofriendlyln', $router);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $this->_setRequest($request);

        if (Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled() && $this->_matchCategoryPager($request)) {
            return true;
        }

        if (Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled() && $this->_matchCategoryLayer()) {
            return true;
        }

        return false;
    }

    public function parseLayeredParams()
    {
        $this->_setRequest(Mage::app()->getRequest());
        $layerParams = $this->_getRequest()->getParam('seo_layered_params', array());
        foreach ($layerParams as $params) {
            $param = explode(Mage::helper('seofriendlyln/config')->getAttributeParamDelimiter(), $params, 2);
            if (empty($param)) {
                continue;
            }
            if ($cat = $this->_getCategoryByParam($param)) {
                $this->_setCategoryToRequest($cat);
            }
            else if ($this->_isHiddenAttribute($param)) {
                if ($this->_isHiddenPriceAttribute($param)) {
                    $this->_setPriceAttributeToRequest($param);
                }
                else {
                    $this->_setHiddenAttributeToRequest($param);
                }
            }
            else {
                $this->_setNotHiddenAttributeToRequest($param);
            }
        }
    }

    protected function _matchCategoryPager($request)
    {
        $pagerUrlFormat = Mage::helper('seofriendlyln/config')->getPagerUrlFormat();
        if (!$pagerUrlFormat) {
            return false;
        }

        $url    = $request->getRequestUri();
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        ///MageWorx fix
        if(strlen($suffix) > 1 and strpos($suffix, '.') === false ){
            $suffix = '.' . $suffix;
        }
        ///MageWorx fix end

        $pagerUrlFormatRegEx = explode('[page_number]', $pagerUrlFormat);
        foreach ($pagerUrlFormatRegEx as $key => $part) {
            $pagerUrlFormatRegEx[$key] = preg_quote($part, '/');
        }
        $pagerUrlFormatRegEx = implode('([0-9]+)', $pagerUrlFormatRegEx);
        if (preg_match('/' . $pagerUrlFormatRegEx . preg_quote($suffix, '/') . '/', $url, $match)) {

            $url = str_replace($match[0], $suffix, $url);
            $request->setRequestUri($url);

            $path = $request->getPathInfo();
            $path = str_replace($match[0], $suffix, $path);
            $request->setPathInfo($path);
            $request->setParam('p', $match[1]);
        }
        else {
            return false;
        }

        $identifier = trim(($suffix && substr($request->getPathInfo(), -(strlen($suffix))) == $suffix ? substr($request->getPathInfo(),
                    0, -(strlen($suffix))) : $request->getPathInfo()), '/');


        $urlSplit = explode('/' . Mage::helper('seofriendlyln/config')->getLayeredNavigationIdentifier() . '/', $identifier, 2);
        if (isset($urlSplit[1])) {
            return false;
        }

        $productUrl = Mage::getModel('catalog/product_url');
        $cat        = $identifier;
        $_params    = array();

        $catPath       = $cat . $suffix;
        $isVersionEE13 = ('true' == (string) Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
        if ($isVersionEE13) {
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');
            /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */

            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catPath);
        }
        else {
            $urlRewrite = Mage::getModel('core/url_rewrite');
            /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */

            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catPath);
        }

        // todo check in ee13
        if (!$urlRewrite->getId()) {
            $store = $request->getParam('___from_store');
            $store = Mage::app()->getStore($store)->getId();
            if (!$store) {
                return false;
            }

            $urlRewrite->setData(array())
                ->setStoreId($store)
                ->loadByRequestPath($catPath);

            if (!$urlRewrite->getId()) {
                return false;
            }
        }
        if ($urlRewrite && $urlRewrite->getId()) {
            $request->setPathInfo($catPath);
            $request->setModuleName('catalog')
                ->setControllerName('category')
                ->setActionName('view')
                ->setParam('id', $urlRewrite->getCategoryId())
                ->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'catalog');
            $urlRewrite->rewrite($request);
            return true;
        }
        return false;
    }

    protected function _matchProductAndCategory()
    {
        list($catPath, $layerParams) = $this->_getUrlData();
        if (!isset($layerParams) || !isset($catPath)) {
            return false;
        }

        $urlRewrite = $this->_getCategoryUrlRewrite($catPath);
        if ($urlRewrite && $urlRewrite->getId()) {
            $this->_prepareRequestForUrlRewrite($urlRewrite, $catPath);

            if (count($layerParams)) {
                $this->_passLayerParamsToRequest($layerParams);
                $urlRewrite->rewrite($this->_getRequest());
                return true;
            }
        }
        return false;
    }

    protected function _matchCategoryLayer()
    {
        list($catPath, $layerParams) = $this->_getUrlData();
        if (!isset($layerParams) || !isset($catPath)) {
            return false;
        }

        $urlRewrite = $this->_getCategoryUrlRewrite($catPath);

        if ($urlRewrite && $urlRewrite->getId()) {
            $this->_prepareRequestForUrlRewrite($urlRewrite, $catPath);

            if (count($layerParams)) {
                $this->_passLayerParamsToRequest($layerParams);
                $isVersionEE13 = ('true' == (string) Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
                if ($isVersionEE13) {
                    $this->_getRequest()->setParams($layerParams)
                        ->setParam('id', $urlRewrite->getValueId())
                        ->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $catPath);
                }
                else {
                    $this->_getRequest()->setParams($layerParams);
                    $urlRewrite->rewrite($this->_getRequest());
                }
                return true;
            }
        }
        return false;
    }

    protected function _prepareRequestForUrlRewrite($urlRewrite, $catPath)
    {
        $this->_getRequest()->setPathInfo($catPath);
        $this->_getRequest()->setModuleName('catalog')
            ->setControllerName('category')
            ->setActionName('view')
            ->setParam('id', $urlRewrite->getCategoryId())
            ->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'catalog'
        );
    }

    protected function _getFilterableCategoryAttributesArray($category)
    {
        $modelLayer = Mage::getModel('catalog/layer')->setData('current_category', $category);
        $attributes = $modelLayer->getFilterableAttributes();
        $attr       = array();

        foreach ($attributes as $attribute) {
            foreach ($attribute->getSource()->getAllOptions() as $option) {
                $attr[] = $option['label'];
            }
        }

        return $attr;
    }

    protected function _isCategoryNameDuplicatesAttribute($category)
    {
        $attributes = $this->_getFilterableCategoryAttributesArray($category);
        return in_array($category->getName(), $attributes);
    }

    /**
     * @param string $catPath
     * @return mix
     */
    protected function _getCategoryUrlRewrite($catPath)
    {
        $isVersionEE13 = ('true' == (string) Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
        if ($isVersionEE13) {
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');
            /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */

            ///MageWorx fix for Enterprise_UrlRewrite 1.12.0.13

            if (!is_array($catPath)) {
                $catArray = $this->_getSystemPaths($catPath);
            }else{
                $catArray = $catPath;
            }
            ///MageWorx fix end

             $urlRewrite->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catArray);

            ///MageWorx fix: cat id from target_path != value_id. Fix category id. Category id as in target_path:
            if($urlRewrite->getId()){
            	$targetPathParts = explode('/', $urlRewrite->getData('target_path'));
            	$cateroryIdFromPath = array_pop($targetPathParts);
            	if($cateroryIdFromPath && is_numeric($cateroryIdFromPath) && $urlRewrite->getData('value_id')){
            		if($cateroryIdFromPath != $urlRewrite->getData('value_id')){
            			$urlRewrite->setData('value_id', $cateroryIdFromPath);
            		}
            	}
            }
            ///MageWorx fix end
        }
        else {
            $urlRewrite = Mage::getModel('core/url_rewrite');
            /* @var $urlRewrite Mage_Core_Model_Url_Rewrite */
            if (is_array($catPath)) {
                $catPath = array_shift($catPath);
            }
            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catPath);
        }

        // todo check in ee13
        if (!$urlRewrite->getId()) {
            $store = $this->_getRequest()->getParam('___from_store');
            $store = Mage::app()->getStore($store)->getId();
            if (!$store) {
                return false;
            }

            $urlRewrite->setData(array())
                ->setStoreId($store)
                ->loadByRequestPath($catPath);

            if (!$urlRewrite->getId()) {
                return false;
            }
        }
        return ($urlRewrite->getId()) ? $urlRewrite : null;
    }

    /**
     * Return request path pieces
     *
     * @param string $requestPath
     * @return array
     */
    protected function _getSystemPaths($requestPath)
    {
        if (version_compare(Mage::getConfig()->getModuleConfig("Enterprise_UrlRewrite")->version, '1.12.0.13', '<')) {
            $systemPath = explode('/', $requestPath);
            $suffixPart = array_pop($systemPath);
            if (false !== strrpos($suffixPart, '.')) {
                $suffixPart = substr($suffixPart, 0, strrpos($suffixPart, '.'));
            }
            $systemPath[] = $suffixPart;
            return $systemPath;
        }
        else {
            $parts  = explode('/', $requestPath);
            $suffix = array_pop($parts);
            if (false !== strrpos($suffix, '.')) {
                $suffix = substr($suffix, 0, strrpos($suffix, '.'));
            }
            $paths = array('request' => $requestPath, 'suffix'  => $suffix);
            if (count($parts)) {
                $paths['whole'] = implode('/', $parts) . '/' . $suffix;
            }
            return $paths;
        }
    }

    protected function _getUrlData()
    {
        if (!$this->_urlData) {
            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            ///MageWorx fix
            if (strlen($suffix) > 1 and strpos($suffix, '.') === false) {
                $suffix = '.' . $suffix;
            }
            ///MageWorx fix end

            $request = $this->_getRequest();
            if ($request->getPathInfo() !== $request->getOriginalPathInfo()) {
                $request->setPathInfo($request->getOriginalPathInfo());
            }


            /**
             * @TODO test and optimizate
             */

             /*
              $identifier = trim(($suffix && substr($request->getPathInfo(), - (strlen($suffix))) == $suffix ? substr($request->getPathInfo(),
              0, -(strlen($suffix))) : $request->getPathInfo()), '/');
             */

            if ($suffix && substr($request->getPathInfo(), - (strlen($suffix))) == $suffix) {
                $identifier = trim(substr($request->getPathInfo(), 0, -(strlen($suffix))), '/');

                if (Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled()) {
                    $pagerUrlFormat = Mage::helper('seofriendlyln/config')->getPagerUrlFormat();
                }

                if (!empty($pagerUrlFormat)) {
                    $pagerPart = str_replace('[page_number]', '', $pagerUrlFormat);
                    if ($pagerPart && $pos       = strpos($identifier, $pagerPart)) {
                        $identifier = substr($identifier, 0, $pos);
                    }
                }
            }
            else {
                $identifier = trim($request->getPathInfo(), '/');

                if (Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled()) {
                    $pagerUrlFormat = Mage::helper('seofriendlyln/config')->getPagerUrlFormat();
                }

                if (!empty($pagerUrlFormat)) {
                    $pagerPart = str_replace('[page_number]', '', $pagerUrlFormat);
                    if ($pagerPart && $pos       = strpos($identifier, $pagerPart)) {
                        $identifier = substr($identifier, 0, $pos);
                    }
                }
            }

            $urlSplit = explode('/' . Mage::helper('seofriendlyln/config')->getLayeredNavigationIdentifier() . '/', $identifier, 2);
            if (isset($urlSplit[1])) {
                $urlSplit[1] = explode('/', $urlSplit[1]);
            }
            else {
                $urlSplit[1] = array();
            }
            $urlSplit[0] .= $suffix;
            $this->_urlData = $urlSplit;
        }
        return $this->_urlData;
    }

    protected function _getCategoryPathInArray()
    {
        $urlData = $this->_getUrlData();
        return $urlData[1];
    }

    protected function _getCategoryByParam($param)
    {
        return false;
        if (count($param) == 1 && !$this->_getRequest()->getQuery('cat')) {
            $productUrl = Mage::getModel('catalog/product_url');

            $cat = Mage::getModel('seofriendlyln/catalog_category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByAttribute('url_key', $productUrl->formatUrlKey($param[0]));
            if (!$cat) {
                $name = str_replace('-', ' ', $productUrl->formatUrlKey($param[0]));
                $cat  = Mage::getModel('seofriendlyln/catalog_category')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->loadByAttribute('name', $name);
            }
            if ($cat && $cat->getId() && !in_array($cat->getUrlKey(), $this->_getCategoryPathInArray())) {
                if (!$this->_isCategoryNameDuplicatesAttribute($cat)) {
                    return $cat;
                }
            }
        }

        return false;
    }

    protected function _isHiddenAttribute($param)
    {
        return count($param) == 1;
    }

    protected function _isHiddenPriceAttribute($param)
    {
        $localParam = explode(Mage::helper('seofriendlyln/config')->getAttributeValueDelimiter(), $param[0]);
        return count($localParam) == 2 && $localParam[0] === 'price';
    }

    protected function _setHiddenAttributeToRequest($param)
    {
        $attr = Mage::helper('seofriendlyln')->_getFilterableAttributes($this->_getCategoryId());
        foreach ($attr as $attrCode => $attrData) {
            if (isset($attrData['options'][$param[0]])) {
                $this->_getRequest()->setQuery($attrCode, $attrData['options'][$param[0]]);
                break;
            }
        }
    }

    protected function _setPriceAttributeToRequest($param)
    {
        $priceParam = explode(Mage::helper('seofriendlyln/config')->getAttributeValueDelimiter(), $param[0]);
        $this->_getRequest()->setQuery($priceParam[0], $priceParam[1]);
    }

    protected function _setCategoryToRequest($cat)
    {
        $this->_getRequest()->setQuery('cat', $cat->getName());
    }

    protected function _getCategoryId()
    {
        $catId   = false;
        $catPath = $this->_getUrlData();

        if ($catPath) {
            ///MageWorx fix for enterprise ~ 1.13.1.0
            //$catRewriteModel = $this->_getCategoryUrlRewrite($catPath);
            $catRewriteModel = $this->_getCategoryUrlRewrite($catPath[0]);
            ///MageWorx fix end
            if ($catRewriteModel) {
                $catId         = $catRewriteModel->getCategoryId();
                $isVersionEE13 = ('true' == (string) Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
                if ($isVersionEE13) {
                    $catId = $catRewriteModel->getValueId();
                }
            }
        }
        return $catId;
    }

    protected function _setNotHiddenAttributeToRequest($param)
    {
        $code  = $param[0];
        $value = $param[1];
        if ($code == 'price') {
            // custom!!
            if (strpos($value, '-') !== false) {
                $multipliers = explode('-', $value);
                $priceFrom   = floatval($multipliers[0]);
                $priceTo     = ($multipliers[1] ? floatval($multipliers[1]) + 0.01 : $multipliers[1]);
                $value       = $priceFrom . '-' . $priceTo;
            }
            $this->_getRequest()->setQuery($code, $value);
        }

        $attr = Mage::helper('seofriendlyln')->_getFilterableAttributes($this->_getCategoryId());
        if (isset($attr) && !empty($attr)) {
            $code = str_replace('-', '_', $code); // attrCode is only = [a-z0-9_]
            if (isset($attr[$code]) && isset($attr[$code]['options'][$value])) {
                $this->_getRequest()->setQuery($code, $attr[$code]['options'][$value]);
            }
        }
    }

    protected function _passLayerParamsToRequest($layerParams)
    {
        if (empty($layerParams)) {
            return;
        }
        $this->_getRequest()->setParam('seo_layered_params', $layerParams);
        return $this;
    }

    protected function _setRequest($request)
    {
        $this->_request = $request;
    }

    protected function _getRequest()
    {
        return $this->_request;
    }
}