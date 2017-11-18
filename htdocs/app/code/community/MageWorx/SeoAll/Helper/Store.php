<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoAll_Helper_Store extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @return array
     */
    public function getAllEnabledStoreIds()
    {
        $stores   = $this->getAllEnabledStore();
        $storeIds = array();
        foreach ($stores as $store) {
            $storeIds[] = $store->getStoreId();
        }
        return $storeIds;
    }

    /**
     *
     * @return array
     */
    public function getAllEnabledStore()
    {
        $allStores = Mage::app()->getStores();
        $stores    = array();
        foreach ($allStores as $store) {
            if ($store->getIsActive() == 1) {
                $stores[] = $store;
            }
        }
        return $stores;
    }

    /**
     * Retrieve store ids
     * @return array
     */
    public function getAllStoreIds()
    {
        $allStores  = Mage::app()->getStores();
        $storeIds[] = 0;
        foreach ($allStores as $_storeId => $store) {
            $storeIds[] = $_storeId;
        }
        return $storeIds;
    }

    /**
     * @param int|string $storeId
     * @return bool
     */
    public function isActiveStore($storeId)
    {
        return in_array($storeId, $this->getAllEnabledStoreIds());
    }

    /**
     * @param null|int|string $storeId
     * @return string
     */
    public function getStoreBaseUrl($storeId = null)
    {
        $url = Mage::app()->getStore($storeId)->getUrl();
        $cropUrl = (strpos($url, "?")) ? substr($url, 0, strpos($url, "?")) : $url;
        return rtrim($cropUrl, '/') . '/';
    }

    /**
     * @param int $id
     * @return Mage_Core_Model_Store
     */
    public function getStoreById($id)
    {
        return $storeData = Mage::getModel('core/store')->load($id);
    }
}