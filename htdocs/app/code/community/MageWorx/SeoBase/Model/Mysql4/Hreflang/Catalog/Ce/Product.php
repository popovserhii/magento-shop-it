<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_Ce_Product extends MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_AbstractProduct
{
    /**
     * {@inheritDoc}
     */
    public function getAllProductUrls($storeIds, $arrayTargetPath = false, $productId = false, $categoryId = false)
    {
        $read          = $this->_getReadAdapter();
        $alternateUrls = array();
        if (!$productId) {
            $this->_select = $read->select()
                ->from($this->getTable('core/url_rewrite'),
                    array('store_id', 'product_id', 'request_path', 'target_path'))
                ->where('target_path IN (?)', $arrayTargetPath)
                ->where('store_id IN(?)', $storeIds)
                ->where('is_system=1')
                ->group(array('store_id', 'product_id'));
        }
        else {
            $useCategories = Mage::getStoreConfigFlag('catalog/seo/product_use_categories');
            $this->_select = $read->select()
                ->from($this->getTable('core/url_rewrite'),
                    array('store_id', 'product_id', 'request_path', 'target_path'))
                ->where('target_path LIKE "%catalog/product/view/id/' . $productId . '%"')
                ->where('product_id=' . $productId)
                ->where('store_id IN(?)', $storeIds)
                ->where('is_system=1')
                ->group(array('store_id', 'product_id'));

            if($categoryId === false){
                $this->_select->where('category_id IS NULL');
            }elseif(is_numeric($categoryId)){
                if($useCategories){
                    $this->_select->where('category_id=' . $categoryId);
                }else{
                    $this->_select->where('category_id IS NULL');
                }
            }
        }

//        var_dump($this->_select->__toString()); exit;

        $query    = $read->query($this->_select);
        $result   = $query->fetchAll();
        $products = array();

        foreach ($result as $row) {
            if (array_key_exists($row['product_id'], $products)) {
                $alternateUrls = array();
                if (isset($products[$row['product_id']]['alternateUrls'])) {
                    $alternateUrls = $products[$row['product_id']]['alternateUrls'];
                }
            }
            else {
                $products[$row['product_id']] = array();
                $alternateUrls                = array();
            }

            $alternateUrls[$row['store_id']] = $this->_baseStoreUrls[$row['store_id']] . $row['request_path'];
            $products[$row['product_id']]    = array('requestPath'   => $row['request_path'], 'alternateUrls' => $alternateUrls);
        }

        return $products;
    }
}