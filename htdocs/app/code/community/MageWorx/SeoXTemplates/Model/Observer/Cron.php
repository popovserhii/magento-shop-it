<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Cron extends Mage_Core_Model_Abstract
{
    const PROCESS_ID = 'seoxtemplates';

    private $indexProcess;

    public function __construct()
    {
        $this->indexProcess = Mage::getModel('index/process'); //new Mage_Index_Model_Process();
        $this->indexProcess->setId(self::PROCESS_ID);
    }

    public function unlock()
    {
        $this->indexProcess->unlock();
    }

    public function scheduledGenerateTemplates($role, $typeId)
    {
        if ($this->indexProcess->isLocked()) {
            return;
        }

        // Set an exclusive lock.
        $this->indexProcess->lockAndBlock();

        register_shutdown_function(array($this, "unlock"));

        $errors = array();

        $collection = Mage::getModel("mageworx_seoxtemplates/template_$role")->getCollection();
        $collection->addTypeFilter($typeId);
        $collection->addCronFilter();

        $ids = array();

        foreach ($collection as $model){
            $ids[] = $model->getTemplateId();
            $model->loadItems();

            switch($role){
                case 'category':
                case 'product':
                    $result = $this->_process($role, $model);
                    break;
                case 'blog':
                    $result = $this->_processBlog($role, $model);
                    break;
            }
            if($result !== true){
                $errors[] = $result;
            }
        }

        /* @var $collection Mage_Sitemap_Model_Mysql4_Sitemap_Collection */

        if ($errors &&
            Mage::helper('mageworx_seoxtemplates/config')->isEnabledCronNotify() &&
            Mage::helper('mageworx_seoxtemplates/config')->getErrorEmailRecipient()
        ){
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /* @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    Mage::helper('mageworx_seoxtemplates/config')->getErrorEmailTemplate(),
                    Mage::helper('mageworx_seoxtemplates/config')->getErrorEmailIdentity(),
                    Mage::helper('mageworx_seoxtemplates/config')->getErrorEmailRecipient(),
                    null,
                    array('warnings' => join("\n", $errors))
            );

            $translate->setTranslateInline(true);
        }

        if(isset($model) && is_object($model)){
            if(!empty($role)){
                $proccessForReindex = $model->getReindexProccesses($ids);

                if(!empty($proccessForReindex)){
                    $indexCollection = Mage::getSingleton('index/indexer')->getProcessesCollection()
                        ->addFieldToFilter('indexer_code', array('in' => $proccessForReindex));
                    foreach($indexCollection as $index){
                        //Mage::log($index->getIndexerCode(), null, 'seoxtemplates_reindex.log');
                        $index->reindexAll();
                    }
                }
            }
        }
    }

    /**
     * Process for products and categories. Update start and finish dates.
     *
     * @param MageWorx_SeoXTemplates_Model_Template $model
     * @return string Errors
     */
    protected function _process($role, $model)
    {
        try{
            if ($model->getStoreId() !== '0') {
                $store = Mage::getModel('core/store')->load($model->getStoreId());

                if (!$store->getCode()) {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Store (ID:%s) not found.', $model->getStoreId()));
                }

                $itemCollection = $model->getItemCollectionForApply(0, 999999999, false, null);
                $model->setDateApplyStart(date("Y-m-d H:i:s"));
                $this->_getHelper($role)->getTemplateAdapterByModel($model)->apply($itemCollection, $model, null);
                $model->setDateApplyFinish(date("Y-m-d H:i:s"));
            }
            else{

                $allStores = Mage::helper('mageworx_seoxtemplates/store')->getAllEnabledStoreIds();

                if(is_array($allStores) && count($allStores)){
                    $model->setDateApplyStart(date("Y-m-d H:i:s"));
                }

                foreach($allStores as $nestedStoreId){

                    $itemCollection = $model->getItemCollectionForApply(0, 999999999, false, $nestedStoreId);
                    $this->_getHelper($role)->getTemplateAdapterByModel($model)->apply($itemCollection, $model, $nestedStoreId);

                    if(Mage::helper('mageworx_seoxtemplates/store')->isLastStoreId($nestedStoreId)){
                        $model->setDateApplyFinish(date("Y-m-d H:i:s"));
                    }
                }
            }
            $model->save();
            return true;
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Process for blog posts. Update start and finish dates.
     *
     * @param MageWorx_SeoXTemplates_Model_Template $model
     * @return string Errors
     */
    protected function _processBlog($role, $model)
    {
        try{
            $model->setDateApplyStart(date("Y-m-d H:i:s"));
            $itemCollection = $model->getItemCollectionForApply(0, 999999999, false, null);
            $this->_getHelper($role)->getTemplateAdapterByModel($model)->apply($itemCollection, $model, null);
            $model->setDateApplyFinish(date("Y-m-d H:i:s"));
            $model->save();
            return true;
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function scheduledForPmtGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_meta_title'));
    }

    public function scheduledForPmdGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_meta_description'));
    }

    public function scheduledForPmkGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_meta_keywords'));
    }

    public function scheduledForPnGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_name'));
    }

    public function scheduledForPukGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_url'));
    }

    public function scheduledForPdGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_description'));
    }

    public function scheduledForPsdGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_short_description'));
    }

    public function scheduledForPiaGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('product', Mage::helper('mageworx_seoxtemplates/template_product')->getTypeIdByTypeCode('product_gallery'));
    }

    public function scheduledForCmtGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('category', Mage::helper('mageworx_seoxtemplates/template_category')->getTypeIdByTypeCode('category_meta_title'));
    }

    public function scheduledForCmdGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('category', Mage::helper('mageworx_seoxtemplates/template_category')->getTypeIdByTypeCode('category_meta_description'));
    }

    public function scheduledForCmkGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('category', Mage::helper('mageworx_seoxtemplates/template_category')->getTypeIdByTypeCode('category_meta_keywords'));
    }

    public function scheduledForCdGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('category', Mage::helper('mageworx_seoxtemplates/template_category')->getTypeIdByTypeCode('category_description'));
    }

    public function scheduledForBtGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('blog', Mage::helper('mageworx_seoxtemplates/template_blog')->getTypeIdByTypeCode('blog_title'));
    }

    public function scheduledForBmdGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('blog', Mage::helper('mageworx_seoxtemplates/template_blog')->getTypeIdByTypeCode('blog_meta_description'));
    }

    public function scheduledForBmkGenerateTemplates()
    {
        $this->scheduledGenerateTemplates('blog', Mage::helper('mageworx_seoxtemplates/template_blog')->getTypeIdByTypeCode('blog_meta_keywords'));
    }

    protected function _getHelper($role)
    {
        return Mage::helper("mageworx_seoxtemplates/template_$role");
    }
}