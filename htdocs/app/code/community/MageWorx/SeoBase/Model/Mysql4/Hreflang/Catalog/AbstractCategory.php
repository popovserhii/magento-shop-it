<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoBase_Model_Mysql4_Hreflang_Catalog_AbstractCategory extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_baseStoreUrls = array();

    /**
     * @param array $storeIds
     * @param int|false $categoryId
     * @return array
     */
    abstract public function getAllCategoryUrls($storeIds, $categoryId = false);

    /**
     * Init resource model (catalog/category)
     */
    protected function _construct()
    {
        $this->_init('catalog/category', 'entity_id');
        $this->_baseStoreUrls = Mage::helper('mageworx_seobase/hreflang')->getBaseStoreUrls();
    }
}