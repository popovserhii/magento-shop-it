<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoReports_Adminhtml_Mageworx_Seoreports_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => Mage::helper('seoreports')->getDefaultStoreId()));
        }

        if (!Mage::helper('seoreports')->getProductReportStatus()) {
            Mage::getSingleton('adminhtml/session')
                ->addWarning(Mage::helper('seoreports')->__('You need to generate the report due to recent changes.'));
        }


        $this->_title($this->__('SEO Reports'))->_title($this->__('Products Report'));
        $this->loadLayout()
            ->_setActiveMenu('report/seoreports')
            ->_addBreadcrumb($this->__('SEO Reports'), $this->__('SEO Reports'))
            ->_addBreadcrumb($this->__('Products Report'), $this->__('Products Report'))
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function generateAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('convert_root_head')
                ->setTitle($this->__('SEO Suite'). ': ' . $this->__('Products Report') . ' ' . $this->__('Generation') . '...');
        $this->renderLayout();
    }

    public function runGenerateAction()
    {
        $action  = $this->getRequest()->getParam('action', '');
        $current = intval($this->getRequest()->getParam('current', 0));
        if (!$action) {
            return false;
        }
        $result = array();
        // 'start', 'preparation', 'calculation'
        switch ($action) {
            case 'start':
                // truncate report table
                $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
                $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
                $connection->truncate($tablePrefix . 'seosuite_report_product');
                $action      = 'preparation';
            case 'preparation':
                $limit       = 500;
                $total       = $this->_getTotalProductCount();
                $result      = array();
                if ($current < $total) {
                    $this->_prepareProducts($current, $limit);
                    $current += $limit;
                    if ($current >= $total) {
                        $current = $total;
                        $action  = 'calculation';
                    }
                    $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current,
                        round($current * 100 / $total, 2));
                    $result['url']  = $this->getUrl('*/*/runGenerate/',
                        array('action'  => $action, 'current' => ($action == 'preparation' ? $current : 0)));
                }
                break;
            case 'calculation':
                $limit   = 1;
                $stories = $this->_getStores();
                $total   = count($stories) + 1;

                $result = array();
                if ($current < $total) {
                    if (count($stories) >= $total) {
                        $storeId = 0;
                    }
                    else {
                        if (isset($stories[$current])) {
                            $storeId = $stories[$current];
                        }
                    }
                    $current += $limit;
                    if ($current >= $total) {
                        $current        = $total;
                        $result['stop'] = 1;
                        Mage::helper('seoreports')->setProductReportStatus(1);
                        $this->_getSession()->addSuccess(Mage::helper('seoreports')->__('Report has been successfully generated.'));
                        break;
                    }
                    $this->_calculateProducts($storeId);

                    if ($current <= $limit) {
                        $result['text'] = $this->__('Starting to calculate store\'s product data...');
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current,
                            round(($current - 1) * 100 / ($total - 1), 2));
                    }
                    else {
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current,
                            round(($current - 1) * 100 / ($total - 1), 2));
                    }
                    $result['url'] = $this->getUrl('*/*/runGenerate/', array('action'  => $action, 'current' => $current));
                }
                break;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getProductCollection($store, $from = 0, $limit = 900000000)
    {
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect($this->_getAttributeList());
        $collection->setStore($store);
        $collection->addStoreFilter($store);
        $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
        if ($store) {
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner',
                $store->getId());
        }
        $collection->addAttributeToFilter('visibility', array(2, 3, 4));
        $collection->addAttributeToFilter('status', 1);
        $collection->getSelect()->limit($limit, $from);

        return $collection;
    }

    protected function _getTotalProductCount()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $select     = $this->_getProductCollection(null)->getSelectCountSql();
        $total      = $connection->fetchOne($select);
        return $total;
    }

    protected function _prepareProducts($from, $limit)
    {
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $connection->beginTransaction();

        // no default stories
        $stores = $this->_getStores();
        foreach ($stores as $storeId) {
            if ($storeId == 0) {
                continue;
            }
            $store      = Mage::app()->getStore($storeId);
            $collection = $this->_getProductCollection($store, $from, $limit);
            foreach ($collection as $item) {
                $urlPath = $this->_getUrlPath($item, $storeId);
                if($urlPath && $item->getSku()){
                    $connection->insert($tablePrefix . 'seosuite_report_product',
                        $this->_getPreparedData($item, $storeId)
                    );
                }
            }
        }
        $connection->commit();
    }

    protected function _getStores()
    {
        return Mage::getModel('core/store')->getCollection()->load()->getAllIds();
    }

    protected function _getUrlPath($item, $storeId)
    {
        if($item->getUrlPath()){
            return $item->getUrlPath();
        }
        //For EE > 1.13.0.0
        return str_replace(Mage::getBaseUrl(), '', $item->getProductUrl(array('_store', $storeId)));
    }

    protected function _calculateProducts($storeId)
    {
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $sql = "UPDATE `" . $tablePrefix . "seosuite_report_product` AS srp,
                    (SELECT `prepared_name`, `store_id`, COUNT(*) AS dupl_count FROM `" . $tablePrefix . "seosuite_report_product` WHERE `store_id`=" . intval($storeId) . " AND `prepared_name`!='' GROUP BY `prepared_name`) AS srpr
                    SET srp.`name_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_name`=srpr.`prepared_name` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_name`!='' AND srp.`store_id`=" . intval($storeId);
        $connection->query($sql);

        $sql = "UPDATE `" . $tablePrefix . "seosuite_report_product` AS srp,
                    (SELECT `prepared_meta_title`, `store_id`, COUNT(*) AS dupl_count FROM `" . $tablePrefix . "seosuite_report_product` WHERE `store_id`=" . intval($storeId) . " AND `prepared_meta_title`!='' GROUP BY `prepared_meta_title`) AS srpr
                    SET srp.`meta_title_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_meta_title`=srpr.`prepared_meta_title` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_meta_title`!='' AND srp.`store_id`=" . intval($storeId);

        $sql = "UPDATE `" . $tablePrefix . "seosuite_report_product` AS srp,
                    (SELECT `url`, `store_id`, COUNT(*) AS dupl_count FROM `" . $tablePrefix . "seosuite_report_product` WHERE `store_id`=" . intval($storeId) . " AND `url`!='' GROUP BY `url`) AS srpr
                    SET srp.`url_dupl` = srpr.dupl_count
                    WHERE srp.`url`=srpr.`url` AND srp.`store_id`=srpr.`store_id` AND srp.`url`!='' AND srp.`store_id`=" . intval($storeId);

        $connection->query($sql);
    }

    public function duplicateViewAction()
    {
        if (!Mage::helper('seoreports')->getProductReportStatus()) {
            Mage::getSingleton('adminhtml/session')
                ->addWarning(Mage::helper('seoreports')->__('You need to generate the report due to recent changes.'));
        }
        $this->_title($this->__('SEO Suite'))->_title($this->__('Products Report'))->_title($this->__('View Duplicates'));
        $this->loadLayout()
            ->_setActiveMenu('report/seosuit')
            ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
            ->_addBreadcrumb($this->__('Products Report'), $this->__('Products Report'))
            ->_addBreadcrumb($this->__('View Duplicates'), $this->__('View Duplicates'))
            ->renderLayout();
    }

    public function duplicateViewGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('report/seoreports');
	}

    protected function _getAttributeList()
    {
        $attributes = array('sku', 'url_path', 'type_id', 'meta_title', 'meta_description');
        if(!Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            $attributes[] = 'url_key';
        }
        return $attributes;
    }

    /**
     *
     * @param Magentp_Catalog_Model_Product $item
     * @param int $storeId
     * @return array
     */
    protected function _getPreparedData($item, $storeId)
    {
        $helper = Mage::helper('seoreports');
        $data = array(
            'entity_id'           => null,
            'product_id'          => $item->getId(),
            'store_id'            => $storeId,
            'sku'                 => $item->getSku(),
            'url_path'            => $this->_getUrlPath($item, $storeId),
            'type_id'             => $item->getTypeId(),
            'name'                => $item->getCustomName(),
            'prepared_name'       => $helper->_prepareText($item->getCustomName()),
            'meta_title'          => $helper->_trimText($item->getMetaTitle()),
            'prepared_meta_title' => $helper->_prepareText($item->getMetaTitle()),
            'meta_title_len'      => $helper->mbStrLenSafety($item->getMetaTitle()),
            'meta_descr_len'      => $helper->mbStrLenSafety($item->getMetaDescription()),
        );

        if(!Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            $data['url'] = $item->getUrlKey();
        }

        return $data;
    }

}