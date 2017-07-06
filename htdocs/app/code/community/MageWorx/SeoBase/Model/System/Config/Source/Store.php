<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_System_Config_Source_Store
{
    protected $_options;

    public function toOptionArray()
    {
        $stores = Mage::helper('mageworx_seobase/hreflang')->getAllEnabledStore(null);
        $values = array();

        foreach ($stores as $store) {

            $sortOrder        = $store->getSortOrder();
            $websiteId        = $store->getWebsite()->getId();
            $websiteName      = $store->getWebsite()->getName();
            $websiteSortOrder = $store->getWebsite()->getSortOrder();
            $storeName        = $store->getName();
            $storeCode        = $store->getCode();
            $storeId          = $store->getStoreId();

            $value = $websiteName . " | " . $storeName . " (code: " . $storeCode . " | ID: " . $storeId . ")";

            $values[] = array(
                'label'              => $value,
                'value'              => $storeId,
                'website_id'         => $websiteId,
                'website_sort_order' => $websiteSortOrder,
                'store_sort_order'   => $sortOrder
            );
        }

        usort($values, array($this, "_cmp"));

        return $values;
    }

    protected function _cmp($a, $b)
    {
        //$orderBy = array('website_sort_order' => 'desc', 'website_id' => 'asc', 'store_sort_order' => 'desc', 'value' => 'asc');
        $orderBy = array('website_id' => 'asc', 'value' => 'asc');
        $result = 0;
        foreach ($orderBy as $key => $value) {
            if ($a[$key] == $b[$key]) continue;
            $result = ($a[$key] < $b[$key]) ? -1 : 1;
            if ($value == 'desc') $result = -$result;
            break;
        }
        return $result;
    }
}