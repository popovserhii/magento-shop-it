<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Mysql4_Catalog_Product_Ce_Xml extends MageWorx_XSitemap_Model_Mysql4_Catalog_Product_AbstractXml
{

    /**
     * Get category collection array
     *
     * @return array
     */
    public function getCollection($storeId, $onlyCount = false, $limit = 4000000000, $from = 0)
    {
        $products = array();

        $store = Mage::app()->getStore($storeId);
        /* @var $store Mage_Core_Model_Store */

        if (!$store) {
            return false;
        }

        if (self::FILTER_PRODUCT == 1) {
            $fpstring = " AND product_id IN (" . implode(',', $this->_getStoreProductIds($storeId)) . ")";
        }
        else {
            $fpstring = '';
        }

        $read = $this->_getReadAdapter();

        $this->_select = $read->select()
            ->distinct()
            ->from(array('e' => $this->getMainTable()), array(($onlyCount ? 'COUNT(*)' : $this->getIdFieldName())))
            ->join(
                array('w' => $this->getTable('catalog/product_website')), "e.entity_id=w.product_id $fpstring", array()
            )
            ->where('w.website_id=?', $store->getWebsiteId())
            ->limit($limit, $from);

        $excludeAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('exclude_from_sitemap');

        if ($excludeAttr) {
            $this->_select->joinLeft(
                    array('exclude_tbl' => $excludeAttr->getBackend()->getTable()),
                    'exclude_tbl.entity_id = e.entity_id AND exclude_tbl.attribute_id = ' . $excludeAttr->getAttributeId() . new Zend_Db_Expr(" AND store_id =
                    IF(
						((SELECT `exclude`.`value` FROM `{$excludeAttr->getBackend()->getTable()}` AS `exclude` WHERE `exclude`.`entity_id` = `e`.`entity_id` AND `attribute_id` = {$excludeAttr->getAttributeId()} AND `store_id` = $storeId) IS NOT NULL) ,
						(SELECT $storeId),
						(SELECT 0)
					)"),
                    array()
                )
                ->where('exclude_tbl.value=0 OR exclude_tbl.value IS NULL');
        }

        if(Mage::helper('xsitemap')->isExcludeFromXMLOutOfStockProduct($storeId)){
            $cond = 'e.entity_id = csi.product_id';

            if (Mage::getStoreConfig('cataloginventory/item_options/manage_stock', $storeId)) {
                $cond .= ' AND IF (csi.use_config_manage_stock = 1, csi.is_in_stock = 1, (csi.manage_stock = 0 OR (csi.manage_stock = 1 AND csi.is_in_stock = 1)))';
            } else {
                $cond .= ' AND IF (csi.use_config_manage_stock = 1, TRUE, (csi.manage_stock = 0 OR (csi.manage_stock = 1 AND csi.is_in_stock = 1)))';
            }


            $this->_select->join(
                array('csi' => $this->getTable('cataloginventory/stock_item')),
                $cond,
                array('is_in_stock', 'manage_stock', 'use_config_manage_stock'));
        }

        $this->_addFilter($storeId, 'visibility',
            Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(), 'in');

        $this->_addFilter($storeId, 'status', Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(), 'in');

        if ($onlyCount) {
            return $read->fetchOne($this->_select);
        }

        $sort = '';

        if (Mage::helper('xsitemap/adapter_seobase')->isSeoBaseAvailable() &&
            Mage::helper('xsitemap/adapter_seobase')->isSeoBaseCanonicalUrlEnabled($storeId) &&
            Mage::helper('xsitemap/adapter_seobase')->getSeoBaseProductCanonicalType($storeId))
        {
            $productCanonicalType = Mage::helper('xsitemap/adapter_seobase')->getSeoBaseProductCanonicalType($storeId);

            if($productCanonicalType == 3){
                //$suffix  = "AND canonical_url_rewrite.category_id IS NULL";
                $suffix = '';
                $suffix2 = "AND category_id IS NULL";
            }else{
                //$suffix  = "AND canonical_url_rewrite.category_id IS NOT NULL";
                $suffix = '';
                $suffix2 = "AND category_id IS NOT NULL";
            }

            if ($productCanonicalType == 1 || $productCanonicalType == 4) {
                $sort = 'DESC';
            }
            else if ($productCanonicalType == 2 || $productCanonicalType == 5) {
                $sort = 'ASC';
            }
            else {

            }
        }
        else {
            $length = Mage::helper('xsitemap')->getXmlSitemapUrlLength();
            if ($length == 'short') {
                $sort = 'ASC';
            }
            elseif ($length == 'long') {
                $sort = 'DESC';
            }
        }

        if(Mage::getStoreConfigFlag('catalog/seo/product_use_categories', $storeId)){
            $suffix3 = '';
        }else{
            $suffix3 = 'AND `category_id` IS NULL';
        }

        $canonicalAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('canonical_url');
        $urlPathAttr   = Mage::getModel('catalog/product')->getResource()->getAttribute('url_path');

        if (!empty($productCanonicalType) && $canonicalAttr) {

            if($productCanonicalType == '3'){
                $this->_select->columns(array('url' => new Zend_Db_Expr("
                    IFNULL(
                        (IFNULL(
                            (SELECT canonical_url_rewrite.`request_path`
                                FROM `" . $canonicalAttr->getBackend()->getTable() . "` AS canonical_path
                                LEFT JOIN `" . $this->getTable('core/url_rewrite') . "` AS canonical_url_rewrite ON canonical_url_rewrite.`id_path` = canonical_path.`value`
                                WHERE canonical_path.`entity_id` = e.`entity_id` AND canonical_path.`attribute_id` = " . $canonicalAttr->getAttributeId() . " AND canonical_url_rewrite.`store_id` IN (0," . $storeId . ") $suffix" .
                                ($sort ? " ORDER BY LENGTH(canonical_url_rewrite.`request_path`) " . $sort : "") .
                                " LIMIT 1),
                            (SELECT `request_path`
                            FROM `" . $this->getTable('core/url_rewrite') . "`
                            WHERE `product_id`=e.`entity_id` AND `store_id` IN (0," . $storeId . ") AND `is_system`=1 AND `request_path` IS NOT NULL $suffix2" .
                            ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                            " LIMIT 1)
                            )),
                        (SELECT p.`value` FROM `" . $urlPathAttr->getBackend()->getTable() . "` AS p
                         WHERE p.`entity_id` = e.`entity_id` AND p.`attribute_id` = " . $urlPathAttr->getAttributeId() . " AND p.`store_id` IN (0," . $storeId . ") ORDER BY p.`store_id` DESC LIMIT 1
                        )
                    )"
                )));
            }
            else{
                $this->_select->columns(array('url' => new Zend_Db_Expr("
                    IFNULL(
                        (IFNULL(
                            (SELECT canonical_url_rewrite.`request_path`
                            FROM `" . $canonicalAttr->getBackend()->getTable() . "` AS canonical_path
                            LEFT JOIN `" . $this->getTable('core/url_rewrite') . "` AS canonical_url_rewrite ON canonical_url_rewrite.`id_path` = canonical_path.`value`
                            WHERE canonical_path.`entity_id` = e.`entity_id` AND canonical_path.`attribute_id` = " . $canonicalAttr->getAttributeId() . " AND canonical_url_rewrite.`store_id` IN (0," . $storeId . ") $suffix" .
                            ($sort ? " ORDER BY LENGTH(canonical_url_rewrite.`request_path`) " . $sort : "") .
                            " LIMIT 1),
                            (SELECT IF(
                                (SELECT LENGTH((SELECT `request_path`
                                FROM `" . $this->getTable('core/url_rewrite') . "`
                                WHERE `product_id`=e.`entity_id` AND `store_id` IN (0," . $storeId . ") AND `is_system`=1 AND `request_path` IS NOT NULL $suffix2" .
                                ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                                " LIMIT 1)) > 0),
                                (SELECT CONCAT(`request_path`, '@', `category_id`)
                                FROM `" . $this->getTable('core/url_rewrite') . "`
                                WHERE `product_id`=e.`entity_id` AND `store_id` IN (0," . $storeId . ") AND `is_system`=1 AND `request_path` IS NOT NULL $suffix2" .
                                ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                                " LIMIT 1),
                                (SELECT NULL)
                                )
                            )
                        )),
                        (SELECT p.`value` FROM `" . $urlPathAttr->getBackend()->getTable() . "` AS p
                         WHERE p.`entity_id` = e.`entity_id` AND p.`attribute_id` = " . $urlPathAttr->getAttributeId() . " AND p.`store_id` IN (0," . $storeId . ") ORDER BY p.`store_id` DESC LIMIT 1
                        )
                    )")
                ));
            }
        }
        else {
            $this->_select->columns(array('url' => new Zend_Db_Expr("(
                SELECT `request_path`
                FROM `" . $this->getTable('core/url_rewrite') . "`
                WHERE `product_id`=e.`entity_id` AND `store_id`=" . intval($storeId) . " AND `is_system`=1 AND `request_path` IS NOT NULL $suffix3" .
                    ($sort ? " ORDER BY LENGTH(`request_path`) " . $sort : "") .
                    " LIMIT 1)")));
        }

        $crossDomainAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('canonical_cross_domain');

        if ($crossDomainAttr && !empty($productCanonicalType)) {
            $this->_select->joinLeft(
                array('cross_domain_tbl' => $crossDomainAttr->getBackend()->getTable()),
                'cross_domain_tbl.entity_id = e.entity_id AND cross_domain_tbl.attribute_id = ' . $crossDomainAttr->getAttributeId(),
                array('canonical_cross_domain' => 'cross_domain_tbl.value')
            );
        }

        $isAddImage = Mage::helper('xsitemap')->isProductImages();

        if ($isAddImage) {
            $mediaAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('image');
            if ($mediaAttr) {
                $this->_select->joinLeft(
                    array('media_tbl' => $mediaAttr->getBackend()->getTable()),
                    'media_tbl.entity_id = e.entity_id AND media_tbl.attribute_id = ' . $mediaAttr->getAttributeId(),
                    array('media' => 'media_tbl.value')
                );
            }
        }

        $updatedAt = Mage::getModel('catalog/product')->getResource()->getAttribute('updated_at');
        if ($updatedAt) {
            $this->_select->joinLeft(
                array('updatedat_tbl' => $updatedAt->getBackend()->getTable()), 'updatedat_tbl.entity_id = e.entity_id',
                array('updated_at' => 'updatedat_tbl.updated_at')
            );
        }
        $createdAt = Mage::getModel('catalog/product')->getResource()->getAttribute('created_at');
        if ($createdAt) {
            $this->_select->joinLeft(
                array('createdat_tbl' => $createdAt->getBackend()->getTable()), 'createdat_tbl.entity_id = e.entity_id',
                array('created_at' => 'createdat_tbl.created_at')
            );
        }

        $query = $read->query($this->_select);

        while ($row = $query->fetch()) {
            $product = $this->_prepareProduct($row, $isAddImage);
            $products[$product->getId()] = $product;
        }

        return $products;
    }
}