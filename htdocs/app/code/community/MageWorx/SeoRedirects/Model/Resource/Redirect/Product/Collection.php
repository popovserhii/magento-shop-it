<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoredirects/redirect_product');
    }

    /**
     * Map of redirect fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'product_id'   => 'main_table.product_id',
    ));

    /**
     * Add store filter
     *
     * @param int $id
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection
     */
    public function addStoreFilter($id)
    {
        $this->getSelect()->where('main_table.store_id = ?', $id);
        return $this;
    }

    /**
     * Add product filter
     *
     * @param int $id
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection
     */
    public function addProductFilter($id)
    {
        $this->getSelect()->where('main_table.product_id = ?', $id);
        return $this;
    }

    /**
     * Add request path filter
     *
     * @param array|string $requestPaths
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection
     */
    public function addRequestPathsFilter($requestPaths)
    {
        if (!is_array($requestPaths)){
            $requestPaths = array($requestPaths);
        }
        if (is_array($requestPaths)) {
            $this->getSelect()->where('main_table.request_path IN(?)', $requestPaths);
        }
        return $this;
    }

    /**
     * Add category filter
     *
     * @param int $catId
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection
     */
    public function addCategoryFilter($catId)
    {
        $this->getSelect()->where('main_table.category_id = ?', (int)$catId);
        return $this;
    }

    /**
     * Add date filter (all rows older than retrieved period in days) to collection
     *
     * @param int $dayNums
     * @return Mage_Core_Model_Resource_Design_Collection
     */
    public function addDateFilter($dayNums)
    {
        $dayNums = (int)$dayNums;
        $this->getSelect()->where(
            new Zend_Db_Expr("DATE(`main_table`.`date_created`) < (CURDATE()- {$dayNums})")
        );
        return $this;
    }

    /**
     * Add category status enabled filter
     *
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection
     */
    public function addEnableStatusFilter()
    {
        $this->getSelect()->where('main_table.status = ?', MageWorx_SeoRedirects_Model_Source_Yesno::STATUS_ENABLED);
        return $this;
    }

}