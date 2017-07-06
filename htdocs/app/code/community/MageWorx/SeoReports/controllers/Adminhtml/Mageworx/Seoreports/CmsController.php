<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoReports_Adminhtml_Mageworx_Seoreports_CmsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => Mage::helper('seoreports')->getDefaultStoreId()));
        }

        if (!Mage::helper('seoreports')->getCmsReportStatus()){
            Mage::getSingleton('adminhtml/session')
                    ->addWarning(Mage::helper('seoreports')->__('You need to generate the report due to recent changes.'));
        }

        $this->_title($this->__('SEO Suite'))->_title($this->__('CMS Report'));
        $this->loadLayout()
                ->_setActiveMenu('report/seosuit')
                ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
                ->_addBreadcrumb($this->__('CMS Report'), $this->__('CMS Report'))
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
                ->setTitle($this->__('SEO Suite'). ': ' . $this->__('CMS Report') . ' ' . $this->__('Generation') . '...');
        $this->renderLayout();
    }

    public function runGenerateAction()
    {
        $action  = $this->getRequest()->getParam('action', '');
        $current = intval($this->getRequest()->getParam('current', 0));
        if (!$action){
            return false;
        }

        $result = array();
        // 'start', 'preparation', 'calculation'
        switch ($action) {
            case 'start':
                // truncate report table
                $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
                $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
                $connection->truncate($tablePrefix . 'seosuite_report_cms');
                $action      = 'preparation';
            case 'preparation':
                $total          = $this->_getTotalCmsCount();
                $result         = array();
                $this->_prepareCms();
                $current        = $total;
                $action         = 'calculation';
                $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current, round($current * 100 / $total, 2));
                $result['url']  = $this->getUrl('*/*/runGenerate/', array('action'  => $action, 'current' => ($action == 'preparation' ? $current : 0)));
                break;
            case 'calculation':
                $limit          = 1;
                $stories        = $this->_getStores();
                $total          = count($stories) + 1;

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
                        Mage::helper('seoreports')->setCmsReportStatus(1);
                        $this->_getSession()->addSuccess(Mage::helper('seoreports')->__('Report has been successfully generated.'));
                        break;
                    }
                    $this->_calculateCms($storeId);

                    if ($current <= $limit) {
                        $result['text'] = $this->__('Starting to calculate store\'s CMS data...');
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total - 1, $current, round(($current) * 100 / ($total), 2));
                    }
                    else {
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total - 1, $current, round(($current) * 100 / ($total - 1), 2));
                    }
                    $result['url'] = $this->getUrl('*/*/runGenerate/', array('action'  => $action, 'current' => $current));
                }
                break;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getCmsCollection($storeId)
    {
        $collection = Mage::getResourceModel('cms/page_collection')->addFieldToFilter('main_table.is_active', 1);
        $prefix     = '';
        if (Mage::getConfig()->getTablePrefix()) {
            $prefix = Mage::getConfig()->getTablePrefix();
        }
        $collection->getSelect()->joinLeft(array('stores' => $prefix . 'cms_page_store'), 'main_table.page_id=stores.page_id', '')->distinct(true);
        $collection->addFieldToFilter('stores.store_id', array(0, $storeId));
        
        return $collection;
    }

    protected function _getTotalCmsCount()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $select     = Mage::getResourceModel('cms/page_collection')->addFieldToFilter('is_active', 1)->getSelectCountSql();
        $total      = $connection->fetchOne($select);
        return $total;
    }

    protected function _prepareCms()
    {
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $helper      = Mage::helper('seoreports');

        $connection->beginTransaction();

        // no default stories
        $stores = $this->_getStores();
        foreach ($stores as $storeId) {
            if ($storeId == 0){
                continue;
            }
            //$store = Mage::app()->getStore($storeId);
            $collection = $this->_getCmsCollection($storeId);
            foreach ($collection as $item) {
                $metaTitle = $item->getMetaTitle() ? $item->getMetaTitle() : $item->getTitle();
                $connection->insert($tablePrefix . 'seosuite_report_cms', array(
                    'page_id'             => $item->getId(),
                    'store_id'            => $storeId,
                    'url_path'            => $item->getIdentifier(),
                    'heading'             => ($item->getContentHeading() ? $item->getContentHeading() : ''),
                    'prepared_heading'    => $helper->_prepareText($item->getContentHeading()) !== "" ? $helper->_prepareText($item->getContentHeading())
                                : $item->getContentHeading() ? $item->getContentHeading() : '',
                    'meta_title'          => $helper->_trimText($metaTitle),
                    'prepared_meta_title' => $helper->_prepareText($item->getMetaTitle()) !== '' ? $helper->_prepareText($item->getMetaTitle())
                                : $item->getMetaTitle() ? $item->getMetaTitle() : "",
                    'meta_title_len'      => $helper->mbStrLenSafety($item->getMetaTitle()),
                    'meta_descr_len'      => $helper->mbStrLenSafety($item->getMetaDescription())
                ));
            }
        }

        $connection->commit();
    }

    protected function _getStores()
    {
        return Mage::getModel('core/store')->getCollection()->load()->getAllIds();
    }

    protected function _calculateCms($storeId)
    {
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $sql = "UPDATE `" . $tablePrefix . "seosuite_report_cms` AS srp,
                    (SELECT `prepared_heading`, `store_id`, COUNT(*) AS dupl_count FROM `" . $tablePrefix . "seosuite_report_cms` WHERE `store_id`=" . intval($storeId) . " AND `prepared_heading`!='' GROUP BY `prepared_heading`) AS srpr
                    SET srp.`heading_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_heading`=srpr.`prepared_heading` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_heading`!='' AND srp.`store_id`=" . intval($storeId);
        $connection->query($sql);

        $sql = "UPDATE `" . $tablePrefix . "seosuite_report_cms` AS srp,
                    (SELECT `prepared_meta_title`, `store_id`, COUNT(*) AS dupl_count FROM `" . $tablePrefix . "seosuite_report_cms` WHERE `store_id`=" . intval($storeId) . " AND `prepared_meta_title`!='' GROUP BY `prepared_meta_title`) AS srpr
                    SET srp.`meta_title_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_meta_title`=srpr.`prepared_meta_title` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_meta_title`!='' AND srp.`store_id`=" . intval($storeId);
        $connection->query($sql);
    }

    public function duplicateViewAction()
    {
        if (!Mage::helper('seoreports')->getCmsReportStatus()){
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('seoreports')->__('You need to generate the report due to recent changes.'));
        }
        $this->_title($this->__('SEO Suite'))->_title($this->__('CMS Report'))->_title($this->__('View Duplicates'));
        $this->loadLayout()
                ->_setActiveMenu('report/seosuit')
                ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
                ->_addBreadcrumb($this->__('CMS Report'), $this->__('CMS Report'))
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

}