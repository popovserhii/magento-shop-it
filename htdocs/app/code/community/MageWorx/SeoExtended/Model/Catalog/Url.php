<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */class MageWorx_SeoExtended_Model_Catalog_Url extends Mage_Catalog_Model_Url
{
    /**
     * Get unique product request path
     *
     * @param   Varien_Object $product
     * @param   Varien_Object $category
     * @return  string
     */
    public function getProductRequestPath($product, $category)
    {
        $mageCatalogVersion = Mage::getConfig()->getModuleConfig("Mage_Catalog")->version;
        if(version_compare($mageCatalogVersion, '1.4.0.0.38', '<=' )){
            //CE 1.4.0.0 - 1.4.2.0
            return $this->_getProductRequestPathBefore15ver($product, $category);
        }
        if(version_compare($mageCatalogVersion, '1.6.0.0.14', '<' )){
            //CE 1.5-1.6.2
            //EE 1.10 - 1.11
            return $this->_getProductRequestPathBefore17ver($product, $category);
        }
        //CE 1.7 and higher
        //EE 1.12.0.0 - 1.12.0.2
        return $this->_getProductRequestPathSince17ver($product, $category);
    }

    protected function _getProductRequestPathBefore15ver($product, $category)
    {
        if ($product->getUrlKey() == '') {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
        } else {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
        }
        $storeId = $category->getStoreId();
        $suffix  = $this->getProductUrlSuffix($storeId);
        $idPath  = $this->generatePath('id', $product, $category);
        /**
         * Prepare product base request path
         */
        if ($category->getLevel() > 1) {
            $this->_addCategoryUrlPath($category); // To ensure, that category has path either from attribute or generated now
            $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath(), false, $storeId, true);
            $requestPath = $categoryUrl . '/' . $urlKey;
        } else {
            $requestPath = $urlKey;
        }

        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }

        $this->_rewrite = null;
        /**
         * Check $requestPath should be unique
         */
        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();
            $existingRequestPath = str_replace($suffix, '', $existingRequestPath);

            if ($existingRequestPath == $requestPath) {
                return $requestPath.$suffix;
            }
            /**
             * Check if existing request past can be used
             */
            if (!empty($requestPath) && strpos($existingRequestPath, $requestPath) !== false) {
                $existingRequestPath = str_replace($requestPath, '', $existingRequestPath);
                if (preg_match('#^-([0-9]+)$#i', $existingRequestPath)) {
                    return $this->_rewrites[$idPath]->getRequestPath();
                }
            }
        }
        /**
         * Check 2 variants: $requestPath and $requestPath . '-' . $productId
         */
        $validatedPath = $this->getResource()->checkRequestPaths(
            array($requestPath.$suffix, $requestPath.'-'.$product->getId().$suffix),
            $storeId
        );

        if ($validatedPath) {
            return $validatedPath;
        }
        /**
         * Use unique path generator
         */
        return $this->getUnusedPath($storeId, $requestPath.$suffix, $idPath);
    }

    protected function _getProductRequestPathBefore17ver($product, $category)
    {
        if ($product->getUrlKey() == '') {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
        } else {
            $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
        }
        $storeId = $category->getStoreId();
        $suffix  = $this->getProductUrlSuffix($storeId);
        $idPath  = $this->generatePath('id', $product, $category);
        /**
         * Prepare product base request path
         */
        if ($category->getLevel() > 1) {
            // To ensure, that category has path either from attribute or generated now
            $this->_addCategoryUrlPath($category);
            $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath(),
                false, $storeId, true);
            $requestPath = $categoryUrl . '/' . $urlKey;
        } else {
            $requestPath = $urlKey;
        }

        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }

        $this->_rewrite = null;
        /**
         * Check $requestPath should be unique
         */
        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();
            $existingRequestPath = str_replace($suffix, '', $existingRequestPath);

            if ($existingRequestPath == $requestPath) {
                return $requestPath.$suffix;
            }
            /**
             * Check if existing request past can be used
             */
            if ($product->getUrlKey() == '' && !empty($requestPath)
                && strpos($existingRequestPath, $requestPath) !== false
            ) {
                $existingRequestPath = str_replace($requestPath, '', $existingRequestPath);
                if (preg_match('#^-([0-9]+)$#i', $existingRequestPath)) {
                    return $this->_rewrites[$idPath]->getRequestPath();
                }
            }
            /**
             * check if current generated request path is one of the old paths
             */
            $fullPath = $requestPath.$suffix;
            $finalOldTargetPath = $this->getResource()->findFinalTargetPath($fullPath, $storeId);
            if ($finalOldTargetPath && $finalOldTargetPath == $idPath) {
                $this->getResource()->deleteRewrite($fullPath, $storeId);
                return $fullPath;
            }
        }
        /**
         * Check 2 variants: $requestPath and $requestPath . '-' . $productId
         */
        $validatedPath = $this->getResource()->checkRequestPaths(
            array($requestPath.$suffix, $requestPath.'-'.$product->getId().$suffix),
            $storeId
        );

        if ($validatedPath) {
            return $validatedPath;
        }
        /**
         * Use unique path generator
         */
        return $this->getUnusedPath($storeId, $requestPath.$suffix, $idPath);
    }

    protected function _getProductRequestPathSince17ver($product, $category){
        if (!Mage::helper('seoextended/config')->isOptimizedUrlsEnabled())
        {
            return parent::getProductRequestPath($product, $category);
        }
        else
        {
            if ($product->getUrlKey() == '') {
                $urlKey = $this->getProductModel()->formatUrlKey($product->getName());
            } else {
                $urlKey = $this->getProductModel()->formatUrlKey($product->getUrlKey());
            }
            $storeId = $category->getStoreId();
            $suffix  = $this->getProductUrlSuffix($storeId);
            $idPath  = $this->generatePath('id', $product, $category);

            /**
             * Prepare product base request path
             */
            if ($category->getLevel() > 1) {
                // To ensure, that category has path either from attribute or generated now
                $this->_addCategoryUrlPath($category);
                $categoryUrl = Mage::helper('seoextended/category')->getCategoryUrlPath($category->getUrlPath(),
                    false, $storeId, true);
                $requestPath = $categoryUrl . '/' . $urlKey;
            } else {
                $requestPath = $urlKey;
            }

            if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
                $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
            }

            $this->_rewrite = null;
            /**
             * Check $requestPath should be unique
             */
            if (isset($this->_rewrites[$idPath])) {
                $this->_rewrite = $this->_rewrites[$idPath];
                $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();

                if ($existingRequestPath == $requestPath . $suffix) {
                    return $existingRequestPath;
                }

                $existingRequestPath = preg_replace('/' . preg_quote($suffix, '/') . '$/', '', $existingRequestPath);
                /**
                 * Check if existing request past can be used
                 */
                if ($product->getUrlKey() == '' && !empty($requestPath)
                    && strpos($existingRequestPath, $requestPath) === 0
                ) {
                    $existingRequestPath = preg_replace(
                        '/^' . preg_quote($requestPath, '/') . '/', '', $existingRequestPath
                    );
                    if (preg_match('#^-([0-9]+)$#i', $existingRequestPath)) {
                        return $this->_rewrites[$idPath]->getRequestPath();
                    }
                }

                $fullPath = $requestPath.$suffix;
                if ($this->_deleteOldTargetPath($fullPath, $idPath, $storeId)) {
                    return $fullPath;
                }
            }
            /**
             * Check 2 variants: $requestPath and $requestPath . '-' . $productId
             */
            $validatedPath = $this->getResource()->checkRequestPaths(
                array($requestPath.$suffix, $requestPath.'-'.$product->getId().$suffix),
                $storeId
            );

            if ($validatedPath) {
                return $validatedPath;
            }
            /**
             * Use unique path generator
             */
            return $this->getUnusedPath($storeId, $requestPath.$suffix, $idPath);
        }
    }
}
