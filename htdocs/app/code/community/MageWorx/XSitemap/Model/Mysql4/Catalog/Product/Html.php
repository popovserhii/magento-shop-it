<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Mysql4_Catalog_Product_Html extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache = array();

    /**
     * Init resource model (catalog/category)
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     *
     * @return Zend_Db_Select
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type'   => $attribute->getBackendType()
            );
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
                break;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('e.' . $attributeCode . $conditionRule, $value);
        }
        else {
            $this->_select->join(
                    array('t1_' . $attributeCode => $attribute['table']),
                    'e.entity_id=t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id=0', array()
                )
                ->where('t1_' . $attributeCode . '.attribute_id=?', $attribute['attribute_id']);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            }
            else {
                $this->_select->joinLeft(
                        array('t2_' . $attributeCode => $attribute['table']),
                        $this->_getWriteAdapter()->quoteInto('t1_' . $attributeCode . '.entity_id = t2_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.attribute_id = t2_' . $attributeCode . '.attribute_id AND t2_' . $attributeCode . '.store_id=?',
                            $storeId), array()
                    )
                    ->where('IFNULL(t2_' . $attributeCode . '.value, t1_' . $attributeCode . '.value)' . $conditionRule,
                        $value);
            }
        }

        return $this->_select;
    }

    /**
     * @param int
     * @return Varien_Data_Collerction
     */
    public function getCollection($catId)
    {
        $currentCatProducts = $this->_getProductDataByCategory($catId);

        if (!$currentCatProducts) {
            return new Varien_Data_Collection();
        }

        $productUrlToCategory = Mage::helper('xsitemap')->getHtmlSitemapProductUrlType();
        $onlyCount            = false;
        $store                = Mage::app()->getStore();
        $storeId              = $store->getStoreId();
        $read                 = $this->_getReadAdapter();

        $this->_select = $read->select()
            ->distinct()
            ->from(array('e' => $this->getMainTable()), array(($onlyCount ? 'COUNT(*)' : $this->getIdFieldName())))
            ->join(
                array('w' => $this->getTable('catalog/product_website')),
                "e.entity_id=w.product_id  AND product_id IN (" . implode(',', array_keys($currentCatProducts)) . ")",
                array()
            )
            ->where('w.website_id=?', $store->getWebsiteId());

        $name = Mage::getModel('catalog/product')->getResource()->getAttribute('name');

        if ($name) {
            $this->_select->join(
                array('cpev' => $name->getBackend()->getTable(), array($this->getIdFieldName())),
                "cpev.entity_id = e.entity_id", array('name' => 'cpev.value')
            );

            $this->_select->joinRight(
                    array('ea' => Mage::getSingleton('core/resource')->getTableName('eav_attribute')),
                    "cpev.attribute_id = ea.attribute_id" .  new Zend_Db_Expr(" AND cpev.store_id =
                    IF(
						(SELECT `cpev_store`.`value` FROM `{$name->getBackend()->getTable()}` AS `cpev_store` WHERE `cpev_store`.`entity_id` = `e`.`entity_id` AND `attribute_id` = {$name->getAttributeId()} AND `store_id` = $storeId) IS NOT NULL ,
						(SELECT $storeId),
						(SELECT 0)
					)"),
                    array()
                )
                ->where('ea.attribute_code=?', 'name');
        }

        $excludeAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('exclude_from_html_sitemap');
        if ($excludeAttr) {
            $this->_select->joinLeft(
                    array('exclude_tbl' => $excludeAttr->getBackend()->getTable()),
                    'exclude_tbl.entity_id = e.entity_id AND exclude_tbl.attribute_id = ' . $excludeAttr->getAttributeId() . new Zend_Db_Expr(" AND exclude_tbl.store_id =
                    IF(
						(SELECT `exclude`.`value` FROM `{$excludeAttr->getBackend()->getTable()}` AS `exclude` WHERE `exclude`.`entity_id` = `e`.`entity_id` AND `attribute_id` = {$excludeAttr->getAttributeId()} AND `store_id` = $storeId) ,
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


        $suffix  = '';
        $suffix2 = '';
        $sort    = '';

        if ($productUrlToCategory == 'canonical') {
            $suffix  = "AND canonical_url_rewrite.category_id IS NOT NULL";
            $suffix2 = "AND category_id IS NOT NULL";

            $productCanonicalType = Mage::helper('xsitemap/adapter_seobase')->getSeoBaseProductCanonicalType();

            if ($productCanonicalType) {
                if ($productCanonicalType == 1 || $productCanonicalType == 4) {
                    $sort = 'DESC';
                }
                elseif ($productCanonicalType == 2 || $productCanonicalType == 5) {
                    $sort = 'ASC';
                }
                elseif ($productCanonicalType == 3) {

                }
                else {
                    $productUrlToCategory = 'yes';
                }
            }
        }

        if ($productUrlToCategory == 'no') {
            if (!Mage::helper('xsitemap')->isProductUrlUseCategory()) {
                $sort         = '';
                $cropCategory = true;
            }
        }
        elseif ($productUrlToCategory == 'yes') {

        }

        $canonicalAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('canonical_url');
        $urlPathAttr   = Mage::getModel('catalog/product')->getResource()->getAttribute('url_path');
        if (Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            $urlSuffix = Mage::helper('catalog/product')->getProductUrlSuffix($storeId);

            if($urlSuffix){
                $urlSuffix = '.' . $urlSuffix;
            }else{
                $urlSuffix = '';
            }

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
        }
        elseif ($canonicalAttr && $productUrlToCategory == 'canonical') {
            $this->_select->columns(array('url' => new Zend_Db_Expr("
            IFNULL(
                (IFNULL((SELECT canonical_url_rewrite.`request_path`
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
                 WHERE p.`entity_id` = e.`entity_id` AND p.`attribute_id` = " . $urlPathAttr->getAttributeId() . " AND p.`store_id` IN (0," . $storeId . ")  LIMIT 1
                )
            )")));
        }
        else {
            $this->_select->columns(array('url' => new Zend_Db_Expr("(
                SELECT `request_path`
                FROM `" . $this->getTable('core/url_rewrite') . "`
                WHERE `product_id`=e.`entity_id` AND `store_id`=" . intval($storeId) . " AND `is_system`=1 AND `category_id`= " . intval($catId) . " AND `request_path` IS NOT NULL" .
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

        $sortOrder = Mage::helper('xsitemap')->getHtmlSitemapSort();
        if ($sortOrder == 'position') {
            $sortOrder = 'e.entity_id';
        }

        if ($sortOrder) {
            $this->_select->order($sortOrder);
        }

        //echo $this->_select->assemble();

        $query = $read->query($this->_select);

        $collection = new Varien_Data_Collection();
        while ($row        = $query->fetch()) {
            $product = $this->_prepareProduct($row);
            //If use root canonical or Product url without category.
            if ((isset($productCanonicalType) && $productCanonicalType == 3) || (isset($cropCategory) && $cropCategory)) {
                $urlArr = explode('/', $product->getUrl());
                $product->setUrl(end($urlArr));
            }
            if (!in_array($product->getId(), $collection->getAllIds())) {
                $collection->addItem($product);
            }
        }

        return $collection;
    }

    /**
     * Prepare product
     *
     * @param array $productRow
     * @return Varien_Object
     */
    protected function _prepareProduct(array $productRow)
    {
        $product     = new Varien_Object();
        $product->setId($productRow[$this->getIdFieldName()]);
        $productUrl  = !empty($productRow['url']) ? $productRow['url'] : 'catalog/product/view/id/' . $product->getId();
        $productName = $productRow['name'];
        $product->setName($productName);
        $product->setUrl($productUrl);
        return $product;
    }

    protected function _getProductDataByCategory($catId)
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->joinField('category_id', $this->_resources->getTableName('catalog/category_product'), 'category_id',
            "product_id=entity_id", 'category_id = ' . $catId, 'inner');

        $ids = $collection->getAllIds();

        if (count($ids) > 0) {
            return array_combine($ids, $ids);
            return array_combine($ids, array());
        }
        return false;
    }

}