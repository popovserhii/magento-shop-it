<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Resource_Redirect_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoredirects/redirect_product', 'redirect_id');
    }

    /**
     * Process redirect before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        return parent::_beforeSave($object);
    }

    /**
     * Process template before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        return parent::_beforeDelete($object);
    }

    /**
     * Perform actions after object load
     *
     * @param Varien_Object $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        return parent::_afterLoad($object);
    }

    /**
     *
     * @param int $productId
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product
     */
    protected function _deleteProductInfoRelation($productId)
    {
        $this->_getReadAdapter()->delete(
            $this->getTable($this->_productInfoTable),
            $this->_getReadAdapter()->quoteInto('product_id = ?', $productId, 'INTEGER')
        );

        return $this;
    }
}