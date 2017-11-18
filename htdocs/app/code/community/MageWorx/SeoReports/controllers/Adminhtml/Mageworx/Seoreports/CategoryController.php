<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoReports_Adminhtml_Mageworx_Seoreports_CategoryController extends Mage_Adminhtml_Controller_Action
{
    protected $_categories = array();

    public function indexAction()
    {
        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => Mage::helper('seoreports')->getDefaultStoreId()));
        }

        if (!Mage::helper('seoreports')->getCategoryReportStatus()) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('seoreports')
                            ->__('You need to generate the report due to recent changes.'));
        }

        $this->_title($this->__('SEO Suite'))->_title($this->__('Categories Report'));
        $this->loadLayout()
                ->_setActiveMenu('report/seosuit')
                ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
                ->_addBreadcrumb($this->__('Categories Report'), $this->__('Categories Report'))
                ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function generateAction()
    {
        Mage::getSingleton('customer/session')->setReportHash(array());
        $this->loadLayout();
        $this->getLayout()->getBlock('convert_root_head')
                ->setTitle($this->__('SEO Suite'). ': ' . $this->__('Categories Report') . ' ' . $this->__('Generation') . '...');
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
                $connection     = Mage::getSingleton('core/resource')->getConnection('core_write');
                $tablePrefix    = (string) Mage::getConfig()->getTablePrefix();
                $connection->truncate($tablePrefix . 'seosuite_report_category');
                $action         = 'preparation';
            case 'preparation':
                $total          = $this->_getTotalCategoryCount();
                $result         = array();
                $this->_prepareCategories();
                $current        = $total;
                $action         = 'calculation';
                $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current,
                        round($current * 100 / $total, 2));
                $result['url']  = $this->getUrl('*/*/runGenerate/',
                        array('action'  => $action, 'current' => ($action == 'preparation' ? $current : 0)));
                break;
            case 'calculation':
                $limit          = 1;
                $stories        = $this->_getStores();
                $total          = count($stories) + 1;
                $result         = array();
                if (($current < $total)) {
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
                        Mage::helper('seoreports')->setCategoryReportStatus(1);
                        $this->_getSession()->addSuccess(Mage::helper('seoreports')->__('Report has been successfully generated.'));
                        break;
                    }
                    $this->_calculateCategories($storeId);
                    if ($current <= $limit) {
                        $result['text'] = $this->__('Starting to calculate store\'s category data...');
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total - 1,
                                $current, round(($current) * 100 / ($total - 1), 2));
                    }
                    else {
                        $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total - 1,
                                $current, round(($current) * 100 / ($total - 1), 2));
                    }
                    $result['url'] = $this->getUrl('*/*/runGenerate/', array('action'  => $action, 'current' => $current));
                }
                break;
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getCategoryCollection($storeId)
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect(array('name', 'url_path', 'meta_title', 'meta_description'));
        $collection->setStoreId($storeId)->addFieldToFilter('is_active', 1);
        return $collection;
    }

    protected function _getTotalCategoryCount()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $select     = $this->_getCategoryCollection(0)->getSelectCountSql();
        $total      = $connection->fetchOne($select);
        return $total;
    }

    protected function _getChilds($parentId, $storeId)
    {
        $collection = Mage::getModel('catalog/category')->setStoreId($storeId)->getCategories($parentId);
        foreach ($collection as $category) {
            if (isset($this->_categories[$category->getId()])){
                continue;
            }
            $this->_categories[$category->getId()] = $category->getId();
            if ($category->getChildrenCount() > 0) {
                $this->_getChilds($category->getId(), $storeId);
            }
        }
        return $this;
    }

    protected function _prepareCategories()
    {
        $stores      = $this->_getStores();
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        $helper      = Mage::helper('seoreports');
        $connection->beginTransaction();
        foreach ($stores as $storeId) {
            $parent = Mage::app()->getStore($storeId)->getRootCategoryId();
            $this->_getChilds($parent, $storeId);

            foreach ($this->_categories as $category) {
                $item = Mage::getModel('catalog/category')->setStoreId($storeId)->load($category);
                $connection->insert($tablePrefix . 'seosuite_report_category',
                array(
                    'category_id'         => $item->getId(),
                    'store_id'            => $storeId,
                    'level'               => $item->getLevel(),
                    'url_path'            => $item->getUrlPath(),
                    'name'                => $item->getName(),
                    'prepared_name'       => $helper->_prepareText($item->getName()),
                    'meta_title'          => $helper->_trimText($item->getMetaTitle()),
                    'prepared_meta_title' => $this->_getPreparedMetaTitle($item),
                    'meta_title_len'      => $helper->mbStrLenSafety($item->getMetaTitle()),
                    'meta_descr_len'      => $helper->mbStrLenSafety($item->getMetaDescription())
                ));
            }
            $this->_categories = array();
        }
        $connection->commit();
    }

    protected function _getPreparedMetaTitle($model)
    {
        if (Mage::helper('seoreports')->_prepareText($model->getMetaTitle())) {
            return Mage::helper('seoreports')->_prepareText($model->getMetaTitle());
        }
        if ($model->getMetaTitle()) {
            return $model->getMetaTitle();
        }
        return "";
    }

    protected function _getStores()
    {
        return Mage::getModel('core/store')->getCollection()->load()->getAllIds();
    }

    protected function _calculateCategories($storeId)
    {
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $sql = "UPDATE `" . $tablePrefix . "seosuite_report_category` AS srp,
                    (SELECT `prepared_name`, `store_id`, COUNT(*) AS dupl_count FROM `" . $tablePrefix . "seosuite_report_category` WHERE `store_id`=" . intval($storeId) . " AND `prepared_name`!='' GROUP BY `prepared_name`) AS srpr
                    SET srp.`name_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_name`=srpr.`prepared_name` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_name`!='' AND srp.`store_id`=" . intval($storeId);
        $connection->query($sql);

        $sql = "UPDATE `" . $tablePrefix . "seosuite_report_category` AS srp,
                    (SELECT `prepared_meta_title`, `store_id`, COUNT(*) AS dupl_count FROM `" . $tablePrefix . "seosuite_report_category` WHERE `store_id`=" . intval($storeId) . " AND `prepared_meta_title`!='' GROUP BY `prepared_meta_title`) AS srpr
                    SET srp.`meta_title_dupl` = srpr.dupl_count
                    WHERE srp.`prepared_meta_title`=srpr.`prepared_meta_title` AND srp.`store_id`=srpr.`store_id` AND srp.`prepared_meta_title`!='' AND srp.`store_id`=" . intval($storeId);
        $connection->query($sql);
    }

    public function duplicateViewAction()
    {
        if (!Mage::helper('seoreports')->getCategoryReportStatus()){
             Mage::getSingleton('adminhtml/session')
                    ->addWarning(Mage::helper('seoreports')->__('You need to generate the report due to recent changes.'));
        }

        $this->_title($this->__('SEO Suite'))->_title($this->__('Categories Report'))->_title($this->__('View Duplicates'));
        $this->loadLayout()
                ->_setActiveMenu('report/seosuit')
                ->_addBreadcrumb($this->__('SEO Suite'), $this->__('SEO Suite'))
                ->_addBreadcrumb($this->__('Categories Report'), $this->__('Categories Report'))
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