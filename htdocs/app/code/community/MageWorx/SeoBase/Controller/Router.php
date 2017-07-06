<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    public function initControllerRouters($observer)
    {
        $front  = $observer->getEvent()->getFront();
        $router = new MageWorx_SeoBase_Controller_Router();
        $front->addRouter('mageworx_seobase', $router);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $this->_beforeModuleMatch();

        $identifier = trim($request->getPathInfo(), '/');

        $d = explode('/', $identifier);

        if (count($d) < 2) {
            return false;
        }

        if ('reviews' == $d[1]) {

            $productUrlSeoSuffix = Mage::helper('catalog/product')->getProductUrlSuffix();


            if (!Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
                $urlPath = trim($d[0] . $productUrlSeoSuffix, '/');
                $product = Mage::getModel('catalog/product')->loadByAttribute('url_path', $urlPath);
            }

            if (empty($product)) {
                $product = Mage::getModel('catalog/product')->loadByAttribute('url_key', $d[0]);
            }

            if (!$product) {
                return false;
            }

            if (isset($d[2]) && $d[2] != 'category') {
                if (!isset($d[3]) || $d[3] != 'id' || !isset($d[4]) || !intval($d[4])) {
                    return false;
                }
                $reviewId = intval($d[4]);
                $request->setActionName('view')->setParam('id', $reviewId);
            }
            else {
                if (isset($d[3])) {
                    $category   = Mage::getModel('catalog/category')->loadByAttribute('url_key', $d[3]);
                    if ($category && $categoryId = $category->getId()) {
                        $request->setParam('category', $categoryId);
                    }
                }
                $request->setActionName('list')
                    ->setParam('id', $product->getId());
            }

            $request->setModuleName('review')
                ->setControllerName('product')
                ->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, 'reviews'
            );

            return true;
        }

        switch ($d[0]) {
            case 'tag':
                if (!isset($d[1])) {
                    return false;
                }
                if (count($d) > 2 || in_array($d[1], array('index', 'customer', 'list'))) {
                    return false;
                }

                $tag = Mage::getModel('tag/tag')->load(urldecode($d[1]), 'name');

                if (!$tag->getId()) {
                    return false;
                }

                $tagStoreIds = $tag->getVisibleInStoreIds();

                if (empty($tagStoreIds)) {
                    return false;
                }

                if (!in_array(Mage::app()->getStore()->getId(), $tagStoreIds)) {
                    return false;
                }

                $request->setModuleName('tag')->setControllerName('product')->setActionName('list')
                    ->setParam('tagId', $tag->getId());

                break;
            case 'rss':
                if (!isset($d[1]) || !isset($d[2])) {
                    return false;
                }
                if (count($d) > 4 || in_array($d[1], array('order'))) {
                    return false;
                }
                $storeId = Mage::app()->getStore($d[1])->getId();
                $t       = null;
                if ($d[2]{0} == '@') {
                    $t = substr($d[2], 1);
                }
                switch ($t) {
                    case 'new':
                        $request->setActionName('new')
                            ->setParam('store_id', $storeId);
                        break;
                    case 'specials':
                        $request->setActionName('special')
                            ->setParam('cid', $d[3])
                            ->setParam('store_id', $storeId);
                        break;
                    case 'discounts':
                        $request->setActionName('salesrule')
                            ->setParam('cid', $d[3])
                            ->setParam('store_id', $storeId);
                        break;
                    default:
                        $category = Mage::getModel('catalog/category')->setStoreId($storeId)->loadByAttribute('url_key',
                            $d[2]);
                        if (!$category || !$category->getId()) {
                            return false;
                        }
                        $request->setActionName('category')
                            ->setParam('cid', $category->getId())
                            ->setParam('store_id', $storeId);
                }
                $request->setModuleName('rss')
                    ->setControllerName('catalog');
                break;
            default:
                return false;
        }

        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier
        );
        return true;
    }
}