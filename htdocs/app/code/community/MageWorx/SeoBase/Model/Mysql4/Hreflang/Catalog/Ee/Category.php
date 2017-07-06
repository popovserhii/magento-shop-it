<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_Ee_Category extends MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_AbstractCategory
{
    protected $_baseStoreUrls = array();

    protected function _construct()
    {
        $this->_init('catalog/category', 'entity_id');
        $this->_baseStoreUrls = Mage::helper('mageworx_seobase/hreflang')->getBaseStoreUrls();
    }

    public function getAllCategoryUrls($storeIds, $categoryId = false)
    {
        $read = $this->_getReadAdapter();
        $this->_select = $read->select()
            ->from(
                $this->getTable('enterprise_urlrewrite/url_rewrite'),
                array(
                    'request_path',
                    'target_path',
                    'store_id',
                    'category_id' =>  new Zend_Db_Expr("REPLACE(target_path, 'catalog/category/view/id/', '')")
                )
            )
            ->where('store_id IN(?)', $storeIds)
            ->where('is_system = (?)', 1)
            ->where('entity_type = (?)', Enterprise_Catalog_Model_Category::URL_REWRITE_ENTITY_TYPE)
            ->order('store_id');

        if ($categoryId) {
             $this->_select->where('target_path = (?)', 'catalog/category/view/id/' . $categoryId);
        }

        $query = $read->query($this->_select);
        $result = $query->fetchAll();

        $categories = array();

        foreach ($result as $row) {
            if (array_key_exists($row['category_id'], $categories)) {
                $alternateUrls = $categories[$row['category_id']]['alternateUrls'];
            }
            else {
                $categories[$row['category_id']] = array();
                $alternateUrls                 = array();
            }
            $alternateUrls[$row['store_id']] = $this->_addCategorySuffix($this->_baseStoreUrls[$row['store_id']] . $row['request_path'], $row['store_id']);
            $categories[$row['category_id']]   = array('requestPath'   => $row['request_path'], 'alternateUrls' => $alternateUrls);
        }

        return $categories;
    }

    protected function _addCategorySuffix($rawUrl, $storeId)
    {
        $rawSeoSuffix = Mage::helper('catalog/category')->getCategoryUrlSuffix($storeId);
        $seoSuffix = !empty($rawSeoSuffix) ? '.' . $rawSeoSuffix : '';
        return $rawUrl .= $seoSuffix;
    }
}