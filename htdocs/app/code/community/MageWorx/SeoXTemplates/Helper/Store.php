<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Store extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve store ids
     * @return array
     */
    public function getAllStoreIds()
    {
        return Mage::helper('mageworx_seoall/store')->getAllStoreIds();
    }

    /**
     *
     * @param int $id
     * @return boolean
     */
    public function isFirstStoreId($id)
    {
        $storeIds = $this->getAllEnabledStoreIds();
        if ($id == array_shift($storeIds)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param int $id
     * @return boolean
     */
    public function isLastStoreId($id)
    {
        $storeIds = $this->getAllEnabledStoreIds();
        if ($id == array_pop($storeIds)) {
            return true;
        }
        return false;
    }

   /**
    *
    * @param int $id
    * @return int
    * @throws Exception
    */
    public function getNextStoreId($id)
    {
        $storeIds = $this->getAllEnabledStoreIds();

        $keys    = array_keys($storeIds, $id);
        $current = array_shift($keys);
        $lastKey = $current + 1;
        if (array_key_exists($lastKey, $storeIds)) {
            return $storeIds[$lastKey];
        }
        throw new Exception('Next Store ID not found, use "isLastStoreId" check');
    }

    /**
     *
     * @return array
     */
    public function getAllEnabledStoreIds()
    {
        return Mage::helper('mageworx_seoall/store')->getAllEnabledStoreIds();
    }

    /**
     *
     * @return array
     */
    public function getAllEnabledStore()
    {
        return Mage::helper('mageworx_seoall/store')->getAllEnabledStore();
    }

    public function getStoreById($id)
    {
        return $storeData = Mage::getModel('core/store')->load($id);
    }
}
