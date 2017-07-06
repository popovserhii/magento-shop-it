<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Model_Mysql4_Catalog_Product_Ee_Xml extends MageWorx_XSitemap_Model_Mysql4_Catalog_Product_AbstractXml
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

            if($productCanonicalType == 1){
                // 1: Canonical Longest by Path Length
                $sort = 'DESC';
                $eeSuffix = "LENGTH(`url`)";
            }elseif($productCanonicalType == 4){
                // 4: Canonical Longest by Category Counter
                $sort = 'DESC';
                $eeSuffix = "char_length(`url`) - char_length(replace(`url`,'/',''))";
            }elseif($productCanonicalType == 2){
                // 2: Canonical Shortest by Path Length
                $sort = 'ASC';
                $eeSuffix = "LENGTH(`url`)";
            }elseif($productCanonicalType == 5){
                // 5: Canonical Longest by Category Counter
                $sort = 'ASC';
                $eeSuffix = "char_length(`url`) - char_length(replace(`url`,'/',''))";
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

        if(Mage::helper('mageworx_seoall/version')->isEeRewriteActive()){

            $urlSuffix = Mage::helper('catalog/product')->getProductUrlSuffix($storeId);
            if($urlSuffix){
                $urlSuffix = '.' . $urlSuffix;
            }else{
                $urlSuffix = '';
            }

            $productCanonicalType = empty($productCanonicalType) ? 3 : $productCanonicalType;

            if($productCanonicalType == 3){
                $this->_select
                    ->joinLeft(
                        array('ecp' => $this->getTable('enterprise_catalog/product')),
                        'ecp.product_id = e.entity_id ' . 'AND ecp.store_id = ' . $storeId,
                        array()
                    )
                    ->joinLeft(array('euur' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecp.url_rewrite_id = euur.url_rewrite_id AND euur.is_system = 1',
                        array()
                    )
                    ->joinLeft(array('ecp2' => $this->getTable('enterprise_catalog/product')),
                        'ecp2.product_id = e.entity_id AND ecp2.store_id = 0',
                        array()
                    )
                    ->joinLeft(array('euur2' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecp2.url_rewrite_id = euur2.url_rewrite_id',
                        array('url' => 'concat( ' . $this->_getWriteAdapter()->getIfNullSql('euur.request_path', 'euur2.request_path') . ',"' . $urlSuffix . '")')
                    );
            }else{

                $this->_select->columns(array('url' => new Zend_Db_Expr("
                        IFNULL(
                            (SELECT IF(
                                (SELECT LENGTH(rwc.request_path) as `url` FROM `{$this->getMainTable()}` AS `e2`
                                LEFT JOIN `{$this->getTable('enterprise_catalog/product')}` AS `ecp` ON ecp.product_id = `e2`.`entity_id` AND `store_id` =
                                    IF(
                                        (SELECT `ecp2`.`store_id` FROM `{$this->getTable('enterprise_catalog/product')}` AS `ecp2` WHERE `ecp2`.`product_id` = `e2`.`entity_id` AND `store_id` = {$storeId}),
                                        (SELECT {$storeId}),
                                        (SELECT 0)
                                    )
                                JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwp` ON `ecp`.`url_rewrite_id` = `rwp`.`url_rewrite_id`
                                JOIN `{$this->getTable('catalog/category_product')}` AS `ccp` ON `ccp`.`product_id` = `e2`.`entity_id`
                                JOIN `{$this->getTable('enterprise_catalog/category')}` AS `ecc` ON `ecc`.`category_id` = `ccp`.`category_id`
                                JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwc` ON `ecc`.`url_rewrite_id` = `rwc`.`url_rewrite_id`  AND (`rwp`.`entity_type` = 3) AND (`rwc`.`store_id` = {$storeId})
                                WHERE `e2`.`entity_id` = `e`.`entity_id`
                                ORDER BY $eeSuffix $sort LIMIT 1
                                ),
                                (SELECT CONCAT(rwc.request_path, '/' , rwp.identifier, '{$urlSuffix}', '@', ecc.category_id) as `url` FROM `{$this->getMainTable()}` AS `e2`
                                LEFT JOIN `{$this->getTable('enterprise_catalog/product')}` AS `ecp` ON ecp.product_id = `e2`.`entity_id` AND `store_id` =
                                    IF(
                                        (SELECT `ecp2`.`store_id` FROM `{$this->getTable('enterprise_catalog/product')}` AS `ecp2` WHERE `ecp2`.`product_id` = `e2`.`entity_id` AND `store_id` = {$storeId}),
                                        (SELECT {$storeId}),
                                        (SELECT 0)
                                    )
                                JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwp` ON `ecp`.`url_rewrite_id` = `rwp`.`url_rewrite_id`
                                JOIN `{$this->getTable('catalog/category_product')}` AS `ccp` ON `ccp`.`product_id` = `e2`.`entity_id`
                                JOIN `{$this->getTable('enterprise_catalog/category')}` AS `ecc` ON `ecc`.`category_id` = `ccp`.`category_id`
                                JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwc` ON `ecc`.`url_rewrite_id` = `rwc`.`url_rewrite_id`  AND (`rwp`.`entity_type` = 3) AND (`rwc`.`store_id` = {$storeId})
                                WHERE `e2`.`entity_id` = `e`.`entity_id`
                                ORDER BY $eeSuffix $sort LIMIT 1
                                ),
                                (SELECT NULL)
                            ))
                            ,

                            (SELECT CONCAT(rwp.identifier, '{$urlSuffix}') AS `url` FROM `{$this->getMainTable()}` AS `e2`
                                LEFT JOIN `{$this->getTable('enterprise_catalog/product')}` AS `ecp` ON ecp.product_id = `e2`.`entity_id` AND `store_id` =
                                IF(
                                    (SELECT `ecp2`.`store_id` FROM `{$this->getTable('enterprise_catalog/product')}` AS `ecp2` WHERE `ecp2`.`product_id` = `e2`.`entity_id` AND `store_id` = {$storeId}),
                                    (SELECT {$storeId}),
                                    (SELECT 0)
                                )
                                JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwp` ON `ecp`.`url_rewrite_id` = `rwp`.`url_rewrite_id`
                                WHERE `e2`.`entity_id` = `e`.`entity_id` LIMIT 1
                            )
                        )
                    "
                )));
            }
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