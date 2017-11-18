<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Controller_Adminhtml_Seoxtemplates_Template extends Mage_Adminhtml_Controller_Action
{
    protected $_model;

    abstract protected function _createModel();

    public function indexAction()
    {
        $this->_addNotification();
        $this->_init();
        $this->_initAction()->renderLayout();
    }

    protected function _init()
    {
        $this->_initModel();

        $this->_title(Mage::helper('core')->__('Templates'))
            ->_title($this->__('Manage Template'));
    }

    protected function _initModel()
    {
        $templateId = $this->getRequest()->getParam('template_id');
        if($templateId){
            $this->_model = $this->_createModel()->load($templateId);
        }else{
            $this->_model = $this->_createModel();
            $this->_model->setStoreId($this->_getStoreIdFromRequest());
            $this->_model->setTypeId($this->_getTypeIdFromRequest());
        }
        Mage::helper('mageworx_seoxtemplates/factory')->setModel($this->_model);
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/seoxtemplates');

        return $this;
    }

    protected function _getStoreIdFromRequest()
    {
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(true)->getId();
        }
        return Mage::app()->getStore((int) $this->getRequest()->getParam('store', 0))->getId();
    }

    protected function _getTypeIdFromRequest()
    {
        if ($typeId = $this->getRequest()->getParam('type_id')) {
            return $typeId;
        }
    }

    protected function _redirect($path, $arguments = array())
    {
        parent::_redirect($path, $arguments);
    }

    protected function _getHelper()
    {
        return Mage::helper('mageworx_seoxtemplates/factory')->getHelper();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_init();

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

        $id = (int) $this->getRequest()->getParam('template_id');
        if ($this->_model->getId() || $id == 0) {

            if (!empty($data)) {
                $this->_model->setData($data);
            }

            $this->_initAction();

            $itemType = Mage::helper('mageworx_seoxtemplates/factory')->getItemType();
            $this->_addContent($this->getLayout()->createBlock("mageworx_seoxtemplates/adminhtml_template_{$itemType}_edit"))
                ->_addLeft($this->getLayout()->createBlock("mageworx_seoxtemplates/adminhtml_template_{$itemType}_edit_tabs"));

            $this->renderLayout();
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Template do not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        $this->_init();
        $id = (int) $this->getRequest()->getParam('template_id');

        if ($id) {
            try {
                $this->_model->setId($id)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Template was successfully deleted'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('template_id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('templates');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Template(s)'));
        }
        else {
            try {
                foreach ($ids as $id) {
                    $model = $this->_createModel()->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were successfully deleted',
                        count($ids)));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massApplyForAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('templates');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Template(s)'));
        }
        else {
            try {
                foreach ($ids as $id) {
                    $model = $this->_createModel()->load($id);
                    $model->setWriteFor((int) $this->getRequest()->getParam('write_for'))->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated',
                        count($ids)));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massCronAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('templates');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Template(s)'));
        }
        else {
            try {
                foreach ($ids as $id) {
                    $model = $this->_createModel()->load($id);
                    $model->setUseCron((int) $this->getRequest()->getParam('use_cron'))->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated',
                        count($ids)));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _prepareIndividualItemIds($data)
    {
        $itemIds = array();
        if (isset($data['post_individual_items']) && $data['post_individual_items']) {
            $itemIds = explode(',', $data['post_individual_items']);
        }
        return $itemIds;
    }

    public function massApplyAction()
    {
        $this->_init();
        $nextIds = $this->getRequest()->getParam('templates');

        if (!is_array($nextIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Template(s)'));
            $this->_redirect('*/*/index');
        }
        else {
            $currentId = $nextIds[0];
            $nextIds   = implode('-', $nextIds);
            $this->_redirect('*/*/apply', array('template_id' => $currentId, 'next_ids'    => $nextIds));
        }
    }

    public function applyAction()
    {
        $this->loadLayout();
        $this->_initModel();

        $nextIds    = $this->getRequest()->getParam('next_ids');

        if ($this->_model->getStoreId() !== '0') {
            $store = Mage::getModel('core/store')->load($this->_model->getStoreId());

            if (!$store->getCode()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Store (ID:%s) not found.',
                        $this->_model->getStoreId()));
                return $this->_redirect('*/*/index');
            }
        }

        $this->getLayout()->getBlock('apply')->setParams(
            array(
                'template' => $this->_model,
                'next_ids' => $nextIds,
                'test'     => $this->getRequest()->getParam('test')
            )
        );

        $this->getLayout()->getBlock('convert_root_head')
            ->setTitle($this->__('SEO XTemplates') . ': ' . $this->__($this->_model->getName()) . ' ' . $this->__('Generating') . '...');

        $this->renderLayout();
    }

    public function runApplyAction()
    {
        $this->_initModel();
        $nextIds = $this->getRequest()->getParam('next_ids');

        if ($this->_model->getStoreId() == '0') {
            $allStores = Mage::helper('mageworx_seoxtemplates/store')->getAllEnabledStoreIds();
            if (!$this->getRequest()->getParam('nested_store_id')) {
                $nestedStoreId = array_shift($allStores);
            }
            else {
                $nestedStoreId = $this->getRequest()->getParam('nested_store_id');
            }
        }

        $reindex = $this->getRequest()->getParam('reindex', '');
        $test    = $this->getRequest()->getParam('test', '');

        if ($reindex) {
            $result = $this->_reindex($reindex, $nextIds);
        }
        elseif($test == 'start'){
            $name = Mage::helper('mageworx_seoxtemplates')->getCsvFileName($this->_model);
            die($this->getUrl('*/*/csv/', array('_secure' => true, 'file_name' => $name)));
        }
        else {
            if (!empty($nestedStoreId)) {
                $result = $this->_apply($nestedStoreId, $nextIds);
            }
            else {
                $result = $this->_apply(null, $nextIds);
            }

            $result['text'] = $this->__('Generating the \'%s\' template.', $this->_model->getName()) . ' ' . $result['text'];
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _apply($nestedStoreId = null, $nextIds = null)
    {
        $result = array();

        $testMode = $this->getRequest()->getParam('test', false);
        $current  = intval($this->getRequest()->getParam('current', 0));

        if ($current === 0 && !$testMode) {
            if ($nestedStoreId) {
                if (Mage::helper('mageworx_seoxtemplates/store')->isFirstStoreId($nestedStoreId)) {
                    $this->_model->setDateApplyStart(date("Y-m-d H:i:s"))->save();
                }
            }
            else {
                $this->_model->setDateApplyStart(date("Y-m-d H:i:s"))->save();
            }
        }


        $limit = Mage::helper('mageworx_seoxtemplates/config')->getTemplateLimitForCurrentStore();

        $total = $this->_getTotalCount($nestedStoreId);

        if ($current <= $total) {

            $itemCollection = $this->_model->getItemCollectionForApply($current, $limit, false, $nestedStoreId);
            $this->_getHelper()->getTemplateAdapterByModel($this->_model)->apply($itemCollection, $this->_model, $nestedStoreId,
                $testMode);
            $current += $limit;

            if ($current >= $total) {
                $current = $total;

                if (!empty($nestedStoreId) && !Mage::helper('mageworx_seoxtemplates/store')->isLastStoreId($nestedStoreId)) {
                    $lastNestedStoreId = $nestedStoreId;
                    $nestedStoreId     = Mage::helper('mageworx_seoxtemplates/store')->getNextStoreId($nestedStoreId);

                    $result['url'] = $this->getUrl('*/*/runApply/',
                        array(
                        'current'         => 0,
                        'template_id'     => $this->_model->getId(),
                        'nested_store_id' => $nestedStoreId,
                        'next_ids'        => $nextIds,
                        'test'            => $this->getRequest()->getParam('test')
                        )
                    );

                    if ($current == 0 && $total == 0) {
                        $result['text'] = $this->__('No products/categories from \'%s\' store view matching the conditions found. Preparing to generate data for the \'%s\' store view...',
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($lastNestedStoreId)->getName(),
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($nestedStoreId)->getName());
                    }
                    else {
                        $result['text'] = $this->__('Generation for \'%s\' store view has completed... Preparing to generate data for the \'%s\' store view...',
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($lastNestedStoreId)->getName(),
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($nestedStoreId)->getName());
                    }
                }
                else {

                    if (!empty($nestedStoreId)) {
                        $result['text'] = $this->__('Generation for \'%s\' store view has completed... ',
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($nestedStoreId)->getName());
                    }
                    else {
                        $result['text'] = $this->__('Generation for \'%s\' store view has completed... ',
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($this->_model->getStoreId())->getName());
                    }

                    $nextId = $this->_getNextTemplateId($this->_model->getId(), $nextIds);

                    if ($nextId) {
                        $result['url'] = $this->getUrl('*/*/runApply/',
                            array(
                            'template_id' => $nextId,
                            'next_ids'    => $nextIds,
                            'test'        => $this->getRequest()->getParam('test')
                            )
                        );
                    }
                    else {
                        if($testMode){
                            $result['url'] = $this->getUrl('*/*/runApply/',
                            array(
                                'test'     => 'start',
                                'method'   => $testMode,
                                'template_id' => $this->_model->getId(),
                                'next_ids'    => $nextIds,
                                )
                            );
                            $result['text'] .= $this->__('Go to report process...');
                        }else{
                            $result['url'] = $this->getUrl('*/*/runApply/',
                                array(
                                'reindex'     => 'start',
                                'template_id' => $this->_model->getId(),
                                'next_ids'    => $nextIds,
                                )
                            );
                            $result['text'] .= $this->__('Go to reindex process...');
                        }
                    }
                    if (!$testMode) {
                        $this->_model->setDateApplyFinish(date("Y-m-d H:i:s"))->save();
                    }
                }
            }
            else {
                if (!empty($nestedStoreId)) {
                    $result['url'] = $this->getUrl('*/*/runApply/',
                        array('current'         => $current, 'template_id'     => $this->_model->getId(), 'nested_store_id' => $nestedStoreId,
                        'next_ids'        => $nextIds, 'test'            => $this->getRequest()->getParam('test')));
                }
                else {
                    $result['url'] = $this->getUrl('*/*/runApply/',
                        array('current'     => $current, 'template_id' => $this->_model->getId(), 'next_ids'    => $nextIds, 'test'        => $this->getRequest()->getParam('test')));
                }

                $result['text'] = '';
                if (intval($this->getRequest()->getParam('current', 0)) == 0) {
                    if (!empty($nestedStoreId)) {
                        $result['text'] = $this->__('Starting generation \'%s\' store view: ',
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($nestedStoreId)->getName());
                    }
                    else {
                        $result['text'] = $this->__('Starting generation \'%s\' store view: ',
                            Mage::helper('mageworx_seoxtemplates/store')->getStoreById($this->_model->getStoreId())->getName());
                    }
                }

                $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current,
                    round($current * 100 / $total, 2));
            }
        }
        return $result;
    }

    protected function _getNextTemplateId($currentId, $nextIdsAsString = null)
    {
        if (!is_null($nextIdsAsString)) {

            $nextIds = explode('-', $nextIdsAsString);

            if (is_array($nextIds) && count($nextIds)) {
                $currentKeys = array_keys($nextIds, $currentId);
                $nextKey     = $currentKeys[0] + 1;

                if (!empty($nextIds[$nextKey])) {
                    return $nextIds[$nextKey];
                }
            }
        }
        return false;
    }

    protected function _getTotalCount($nestedStoreId)
    {
        return $this->_model->getItemCollectionForApply(0, 999999999, true, $nestedStoreId);
    }

    protected function _reindex($reindex, $nextIds)
    {
        $result = array();
        if ($reindex == 'start') {

            $result['url']  = $this->getUrl('*/*/runApply/',
                array(
                'reindex'     => 'run',
                'template_id' => $this->_model->getTemplateId(),
                'next_ids'    => $nextIds,
                'test'        => $this->getRequest()->getParam('test')
                )
            );
            $result['text'] = $this->__('Starting to re-index product data...');
        }
        elseif ($reindex == 'run') {

            $proccessForReindex = $this->_model->getReindexProccesses($nextIds);
            $current            = intval($this->getRequest()->getParam('current', 0));

            $collection = Mage::getSingleton('index/indexer')
                    ->getProcessesCollection()
                    ->addFieldToFilter('indexer_code', array('in' => $proccessForReindex))->setOrder('process_id', 'ASC');
            $collection->getSelect()->where('process_id > ?', $current)->limit(1, 0);

            $index = $collection->getFirstItem();

            if (!$index->getId()) {
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Template(s) was(were) successfully applied'));
                die($this->getUrl('*/*/index/', array('_secure' => true)));
            }
            else {
                try {
                    $index->reindexAll();
                    $result['text'] = $index->getIndexer()->getDescription() . $this->__('... 100%. Done.');
                }
                catch (Mage_Core_Exception $e) {
                    $result['text'] = $e->getMessage();
                }

                $urlParams     = array(
                    'reindex'     => 'run',
                    'current'     => $index->getId(),
                    'template_id' => $this->_model->getId(),
                    'next_ids'    => $nextIds
                );
                $result['url'] = $this->getUrl('*/*/runApply/', $urlParams);
            }
        }
        else {
            Mage::throwException($this->__('Url param \'reindex\' is wrong.'));
        }
        return $result;
    }

    public function csvAction()
    {
        $name = $this->getRequest()->getParam('file_name');
        $path = Mage::helper('mageworx_seoxtemplates')->getCsvFilePath();
        $file = Mage::helper('mageworx_seoxtemplates')->getCsvFile($path, $name);

        if ($file && file_exists($file)) {
            $content = array('rm'    => true, 'type'  => 'filename', 'value' => $file);
            $date = Mage::getModel('core/date')->date('Y-m-d_H-i-s');
            $this->_prepareDownloadResponse('seoxtemplates_' . $date . '.csv', $content);
            die($this->getUrl('*/*/index/', array('_secure' => true)));
        }
        Mage::getSingleton('adminhtml/session')->addWarning($this->__('Report File not found. Please make sure there are assigned products/categories to the template.'));
        $this->_redirect('*/*/', array('_secure' => true));
    }

    protected function _addNotification()
    {
        $msg = $this->__('We strongly recommend to read the user guide to know how the templates work. We also recommend to use the test option before applying templates to your products. Please note that you cannot undo the process of applying templates to products or categories.<br>');
        $msg .= $this->__('If templates generate empty value, it will be skipped (the previous value will be kept).<br>');
        $msg .= $this->__('To see the data generated by the SEO Extended Templates you should switch to the STORE VIEW level.');


        return Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    protected function _getStoreId()
    {
        return $this->_model->getStoreId();
    }

    protected function _getTypeId()
    {
        return $this->_model->getTypeId();
    }

    protected function _getAssignType()
    {
        return $this->_model->getAssignType();
    }

    protected function _getTemplateId()
    {
        return $this->_model->getTemplateId();
    }

    protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('catalog/mageworx_seoxtemplates');
	}

}
