<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Template_Category extends MageWorx_SeoXTemplates_Model_Template
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_category');
        $this->setIdFieldName('template_id');
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template_Relation_Category
     */
    public function getIndividualRelatedModel()
    {
        return Mage::getSingleton('mageworx_seoxtemplates/template_relation_category');
    }

    /**
     * Set individual items
     */
    public function loadItems()
    {
        if($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
            $itemIds = $this->getIndividualRelatedModel()->getResource()->getItemIds($this->getId());
            $this->setInItems($itemIds);
        }
    }

    /**
     * Retrive filtered collection for apply (or count)
     *
     * @param int $from
     * @param int $limit
     * @param bool $onlyCountFlag
     * @param int|null $nestedStoreId
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function getItemCollectionForApply($from, $limit, $onlyCountFlag = false, $nestedStoreId = null)
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $microtime = microtime(1);

        if ($this->getStoreId() === '0') {
            if ($this->_issetUniqStoreTemplateForAllItems($nestedStoreId)) {
                return 0;
            }
            $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
        }
        elseif ($this->getStoreId()) {
            if ($this->_getHelper()->isAssignForAllItems($this->getAssignType())) {
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
            }
            else {
                $excludeItemIds = false;
            }
        }

        $storeId    = !empty($nestedStoreId) ? $nestedStoreId : $this->getStoreId();
        $rootId     = Mage::app()->getStore($storeId)->getRootCategoryId();

        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection->setStoreId($storeId);
        $collection->addFieldToFilter('path', array('like'=> "1/$rootId/%"));

        if($this->_getHelper()->isWriteForEmpty($this->getWriteFor())){
            $adapter = $this->_getHelper()->getTemplateAdapterByModel($this);
            $attributes = $adapter->getAttributeCodes();

            foreach($attributes as $attribute){
                $collection->addAttributeToSelect($attribute);

                $collection->addAttributeToFilter(
                    array(
                        array('attribute' => $attribute, 'null' => true),
                        array('attribute' => $attribute, 'in' => '')
                    ), null, 'left'
                );
            }
        }


        if ($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())) {
            $assignItems = (is_array($this->getInItems()) && count($this->getInItems())) ? $this->getInItems() : 0;
            $collection->getSelect()->where('e.entity_id IN (?)', $assignItems);
        }
        if (!empty($excludeItemIds)) {
            $collection->getSelect()->where('e.entity_id NOT IN (?)', $excludeItemIds);
        }

        if ($onlyCountFlag) {
//            echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>"; //exit;
            return $collection->count();
        }
        else {
            $collection->addAttributeToSelect('*');
            $collection->getSelect()->limit($limit, $from);

//            echo $collection->getSelect()->__toString(); exit;
//            echo "<pre>"; print_r($collection->getItems()); echo "</pre>"; exit;
//            echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>"; //exit;

            return $collection;
        }
    }

    /**
     *
     * @return array
     */
    public function getAssignForAnalogTemplateCategoryIds()
    {
        $collection = $this->getCollection()
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAssignTypeFilter($this->_getHelper()->getAssignForIndividualItems());
        if ($this->getTemplateId()) {
            $collection->excludeTemplateFilter($this->getTemplateId());
        }

        $templateIDs = $collection->getAllIds();
        $readonlyIds = $this->getIndividualRelatedModel()->getResource()->getItemIds($templateIDs);
        return $readonlyIds;
    }

    /**
     *
     * @param int $nestedStoreId
     * @return array|false
     */
    protected function _getExcludeItemIdsByTemplate($nestedStoreId = null)
    {
        $templateCollection = $this->getCollection();
        $templateCollection->addTypeFilter($this->getTypeId());
        $templateCollection->addAssignTypeFilter($this->_getHelper()->getAssignForIndividualItems());

        if($this->getStoreId() == '0'){
            if($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $templateCollection->addStoreFilter($nestedStoreId);
            }
            elseif($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->excludeTemplateFilter($this->getTemplateId());
            }
        }
        elseif ($this->getStoreId()) {
            if($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
            }
            elseif($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
                return false;
            }
        }

        $templateIds = $templateCollection->getAllIds();

        $excludeItemIds = Mage::getModel("mageworx_seoxtemplates/template_relation_category")->getResource()->getItemIds($templateIds);
        return (!empty($excludeItemIds)) ? $excludeItemIds : false;
    }

    /**
     * Retrine reindex proccesses by templates
     * @param array $nextIds
     * @return array
     */
    public function getReindexProccesses($nextIds)
    {
        $ids = (is_array($nextIds)) ? $nextIds : explode('-', $nextIds);
        if($this->getId()){
            array_push($ids, $this->getId());
        }
        if(!empty($ids)){
            $data = $this->getCollection()->loadByIds($ids)->addFieldToSelect('type_id')->toArray();

            $processes = array();
            if(is_array($data['items']) && count($data['items'])){
                foreach($data['items'] as $item){
                    switch($item['type_id']){
                        case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_DESCRIPTION:
                        case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_TITLE:
                        case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_DESCRIPTION:
                        case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_KEYWORDS:
                            $processes[] = 'catalog_category_flat';
                            $processes[] = 'catalogsearch_fulltext';
                            break;
                    }
                }
            }

            $processes = array_unique($processes);
            return $processes;
        }
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template_Category
     */
    protected function _getHelper()
    {
        return Mage::helper("mageworx_seoxtemplates/template_category");
    }

}
