<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_Ee_Product extends MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_AbstractProduct
{
    public function getAllProductUrls($storeIds, $arrayTargetPath = false, $productId = false, $categoryId = false)
    {
        $categoryId    = null; // Magento EE (> 1.13.x) gets category id from session, but not from current request path
        $alternateUrls = array();
        $read = $this->_getReadAdapter();
        $this->_select = $read->select();

        if($productId) {

            $targetPath = 'catalog/product/view/id/' . $productId;

            if(!$categoryId && Mage::registry('current_category')) {
                $product       = Mage::registry('current_product');
                $categoryIds   = $product->getCategoryIds();
                $rawCategoryId = $this->_getCategoryIdByRequestPath();

                if($categoryIds && in_array($rawCategoryId, $categoryIds)) {
                    $categoryId = $rawCategoryId;
                }
            }

            if($categoryId) {
                $targetPath .= '/category/' . $categoryId;
                $useRootUrls = false;
            } else {
                $useRootUrls = true;
            }

            $arrayTargetPath = array($targetPath);
        } else {
            $useRootUrls = (Mage::helper('mageworx_seobase')->getProductCanonicalType() ==
                MageWorx_SeoBase_Model_Canonical_Product::ROOT);
        }

        if (!empty($arrayTargetPath)) {
            $stringTargetPath = "'" . trim(implode("','", $arrayTargetPath),"'") . "'";

            if($useRootUrls) {

                $concatSql = $read->getConcatSql(
                    array('"catalog/product/view/id/"', 'e.entity_id')
                );

                $this->_select = $read->select()
                    ->from(
                        array('e' => $this->getMainTable()),
                        array(
                            'product_id' => 'e.entity_id',
                            'cstore.store_id',
                            'product_url_part' => new Zend_Db_Expr(
                                $read->getIfNullSql('rwp.identifier', 'rwp_default.identifier')
                                )
                            )
                        )
                    ->join(
                        array(
                            'cstore' => $this->getTable('core/store')
                        ),
                        new Zend_Db_Expr('1'),
                        array()
                        )
                    ->joinLeft(
                        array('ecp' => $this->getTable('enterprise_catalog/product')),
                        'ecp.product_id = e.entity_id ' . 'AND ecp.store_id = cstore.store_id',
                        array()
                    )
                    ->joinLeft(
                        array('rwp' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecp.url_rewrite_id = rwp.url_rewrite_id' . ' AND ' .
                        $read->quoteInto('rwp.entity_type = ?', Enterprise_Catalog_Model_Product::URL_REWRITE_ENTITY_TYPE),
                        array()
                    )
                    ->joinLeft(
                        array('ecp_default' => $this->getTable('enterprise_catalog/product')),
                        'ecp_default.product_id = e.entity_id ' . 'AND ecp_default.store_id = 0',
                        array()
                    )
                    ->joinLeft(
                        array('rwp_default' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'rwp_default.url_rewrite_id = ecp_default.url_rewrite_id' . ' AND ' .
                        $read->quoteInto('rwp_default.entity_type = ?', Enterprise_Catalog_Model_Product::URL_REWRITE_ENTITY_TYPE),
                        array()
                    )
                    ->where('cstore.store_id IN(?)', $storeIds)
                    ->where("$concatSql IN(?)", $arrayTargetPath)
                    ->group(array('e.entity_id', 'cstore.store_id')
                );

            } else {

                $whereCondition = "
                    CONCAT('catalog/product/view/id/', e.entity_id, '/category/', ecc.category_id)
                    IN (" . $stringTargetPath . ")
                    OR
                    CONCAT('catalog/product/view/id/', e.entity_id)
                    IN (" . $stringTargetPath . ")
                ";

                $this->_select = $read->select()
                    ->from(
                        array('e' => $this->getMainTable()),
                        array(
                            'product_id' => 'e.entity_id',
                            'cstore.store_id',
                            'product_url_part' => new Zend_Db_Expr(
                                $read->getIfNullSql('rwp.identifier', 'rwp_default.identifier')
                                ),
                            'category_url_part' => new Zend_Db_Expr(
                                $read->getIfNullSql('rwc.request_path', 'rwc_default.request_path')
                                )
                            )
                        )
                    ->join(
                        array(
                            'cstore' => $this->getTable('core/store')
                        ),
                        new Zend_Db_Expr("1"),
                        array()
                        )
                    ->joinLeft(
                        array('ccp' => $this->getTable('catalog/category_product')),
                        'ccp.product_id = e.entity_id ',
                        array()
                    )
                    ->joinLeft(
                        array('ecc' => $this->getTable('enterprise_catalog/category')),
                        'ecc.category_id = ccp.category_id AND ecc.store_id = cstore.store_id',
                        array()
                    )
                    ->joinLeft(
                        array('rwc' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecc.url_rewrite_id = rwc.url_rewrite_id',
                        array()
                    )
                    ->joinLeft(
                        array('ecc_default' => $this->getTable('enterprise_catalog/category')),
                        'ecc_default.category_id = ccp.category_id AND ecc_default.store_id = 0',
                        array()
                    )
                    ->joinLeft(
                        array('rwc_default' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecc_default.url_rewrite_id = rwc_default.url_rewrite_id',
                        array()
                    )
                    ->joinLeft(
                        array('ecp' => $this->getTable('enterprise_catalog/product')),
                        'ecp.product_id = e.entity_id ' . 'AND ecp.store_id = cstore.store_id',
                        array()
                    )
                    ->joinLeft(
                        array('rwp' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'ecp.url_rewrite_id = rwp.url_rewrite_id' . ' AND ' .
                        $read->quoteInto('rwp.entity_type = ?', Enterprise_Catalog_Model_Product::URL_REWRITE_ENTITY_TYPE),
                        array()
                    )
                    ->joinLeft(
                        array('ecp_default' => $this->getTable('enterprise_catalog/product')),
                        'ecp_default.product_id = e.entity_id ' . 'AND ecp_default.store_id = 0',
                        array()
                    )
                    ->joinLeft(
                        array('rwp_default' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                        'rwp_default.url_rewrite_id = ecp_default.url_rewrite_id'. ' AND ' .
                        $read->quoteInto('rwp_default.entity_type = ?', Enterprise_Catalog_Model_Product::URL_REWRITE_ENTITY_TYPE),
                        array()
                    )

                    ->where('cstore.store_id IN(?)', $storeIds)
                    ->where(new Zend_Db_Expr($whereCondition))
                    ->group(array('e.entity_id', 'cstore.store_id')
                );
            }
        }

        $result = $read->fetchAll($this->_select);

        $products[$productId] = array();
        $products[$productId]['alternateUrls'] = array();

        foreach ($result as $row) {
            if(!empty($row['category_url_part'])) {
                $row['url'] = $row['category_url_part'] . '/' . $row['product_url_part'];
            } else {
                $row['url'] = $row['product_url_part'];
            }

            if (array_key_exists($row['product_id'], $products)) {
                $alternateUrls = $products[$row['product_id']]['alternateUrls'];
            }
            else {
                $products[$row['product_id']]  = array();
                $alternateUrls                 = array();
            }
            $alternateUrls[$row['store_id']] = $this->_addProductSuffix($this->_baseStoreUrls[$row['store_id']] . $row['url'], $row['store_id']);
            $products[$row['product_id']] = array('requestPath'   => $row['url'], 'alternateUrls' => $alternateUrls);
        }

        return $products;
    }

    protected function _getCategoryIdByRequestPath()
    {
        $productUrlKey = Mage::registry('current_product')->getUrlKey();
        $storeId       = Mage::app()->getStore()->getStoreId();
        $requestString = Mage::app()->getRequest()->getRequestString();

        $pos = strpos($requestString, $productUrlKey);

        if($pos !== false) {
            $requestPath = trim(substr_replace($requestString, '', $pos), '/');
        }

        if($requestPath) {
            $read = $this->_getReadAdapter();
            $select = $read->select()
                    ->from(
                        $this->getTable('enterprise_urlrewrite/url_rewrite'),
                        array('target_path')
                    )
                    ->where('request_path = (?)', $requestPath)
                    ->where('store_id = (?)', $storeId)
                    ->where('is_system = (?)', 1)
                    ->where('entity_type = (?)', 2)
                    ->order('store_id');

            $query    = $read->query($select);
            $result   = $query->fetchAll();
            if(!empty($result[0]['target_path'])) {
                return (int)ltrim($result[0]['target_path'], 'catalog/category/view/id/');
            }
        }
        return null;
    }

    protected function _addProductSuffix($rawUrl, $storeId)
    {
        $rawSeoSuffix = Mage::helper('catalog/product')->getProductUrlSuffix($storeId);
        $seoSuffix = !empty($rawSeoSuffix) ? '.' . $rawSeoSuffix : '';
        return $rawUrl .= $seoSuffix;
    }
}

