<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_Ce_Category extends MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_AbstractCategory
{
    protected $_baseStoreUrls = array();

    protected function _construct()
    {
        $this->_init('catalog/category', 'entity_id');
        $this->_baseStoreUrls = Mage::helper('mageworx_seobase/hreflang')->getBaseStoreUrls();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllCategoryUrls($storeIds, $categoryId = false)
    {
        $read = $this->_getReadAdapter();

        if (!$categoryId) {
            $this->_select = $read->select()
                ->from($this->getTable('core/url_rewrite'),
                    array('store_id', 'category_id', 'request_path', 'target_path'))
                ->where('category_id IS NOT NULL')
                ->where('product_id IS NULL')
                ->where('target_path LIKE "%category%"')
                ->where('store_id IN(?)', $storeIds)
                ->where('is_system=1')
                ->order('category_id ASC');
        }
        else {
            $this->_select = $read->select()
                ->from($this->getTable('core/url_rewrite'),
                    array('store_id', 'category_id', 'request_path', 'target_path'))
                ->where('category_id=' . $categoryId)
                ->where('product_id IS NULL')
                ->where('target_path LIKE "%category%"')
                ->where('store_id IN(?)', $storeIds)
                ->where('is_system=1')
                ->order('category_id ASC');
        }

        $query      = $read->query($this->_select);
        $result     = $query->fetchAll();
        $categories = array();

        foreach ($result as $row) {
            if (array_key_exists($row['category_id'], $categories)) {
                $alternateUrls = $categories[$row['category_id']]['alternateUrls'];
            }
            else {
                $categories[$row['category_id']] = array();
                $alternateUrls                 = array();
            }
            $alternateUrls[$row['store_id']] = $this->_baseStoreUrls[$row['store_id']] . $row['request_path'];
            $categories[$row['category_id']]   = array('requestPath'   => $row['request_path'], 'alternateUrls' => $alternateUrls);
        }

        return $categories;
    }
}