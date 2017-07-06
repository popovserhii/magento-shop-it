<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Template_Product extends MageWorx_SeoXTemplates_Model_Template
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_product');
        $this->setIdFieldName('template_id');
    }


    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template_Relation_product
     */
    public function getIndividualRelatedModel()
    {
        return Mage::getSingleton('mageworx_seoxtemplates/template_relation_product');
    }


    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template_Relation_Attributeset
     */
    public function getGroupRelatedModel()
    {
        return Mage::getModel('mageworx_seoxtemplates/template_relation_attributeset');
    }

    /**
     * Set for model related items;
     */
    public function loadItems()
    {
        if($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
            $itemIds = $this->getIndividualRelatedModel()->getResource()->getItemIds($this->getId());
            $this->setInItems($itemIds);
        }elseif($this->_getHelper()->isAssignForGroupItems($this->getAssignType())){

            $groupItemIds = $this->getGroupRelatedModel()->getResource()->getItemIds($this->getId());

            $this->setInGroupItems($groupItemIds);

            $itemIds = array();
            foreach($groupItemIds as $groupItemId){
                $ids = Mage::getModel('catalog/product')->getCollection()
                    ->addFieldToFilter('attribute_set_id', $groupItemId)->getAllIds();
                $itemIds = array_merge($itemIds, $ids);
            }
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
    public function getItemCollectionForApply($from = null, $limit = null, $onlyCountFlag = false, $nestedStoreId = null)
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $microtime = microtime(1);

        if($this->getStoreId() == '0'){
            if($this->_issetUniqStoreTemplateForAllItems($nestedStoreId)){
                return false;
            }

            if($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){

                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
                $assignItems = $this->getInItems();
            }
            elseif($this->_getHelper()->isAssignForGroupItems($this->getAssignType())){
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
                $assignItems = $this->getInItems();
            }
            elseif($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($nestedStoreId);
            }
        }
        elseif($this->getStoreId()){
            if($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
                $excludeItemIds = false;
                $assignItems = $this->getInItems();
                $assignItems = (is_array($assignItems) && count($assignItems)) ? $assignItems : false;
            }
            elseif($this->_getHelper()->isAssignForGroupItems($this->getAssignType())){
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($this->getStoreId());
                $assignItems = $this->getInItems();
                $assignItems = (is_array($assignItems) && count($assignItems)) ? $assignItems : false;
            }
            elseif($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $excludeItemIds = $this->_getExcludeItemIdsByTemplate($this->getStoreId());
            }
        }

        $storeId = !empty($nestedStoreId) ? $nestedStoreId : $this->getStoreId();
        if($onlyCountFlag && empty($assignItems)){
        	$collection = Mage::getModel('catalog/product')->getCollection();
        	$collection->addStoreFilter($storeId);
        	$collection->setStoreId($storeId);
        	$count = $collection->getSize();
        	$finalCount = is_array($excludeItemIds) ? $count - count($excludeItemIds) : $count;

        	return $finalCount;
        }

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addStoreFilter($storeId);
        $collection->setStoreId($storeId);

        if($this->_getHelper()->isWriteForEmpty($this->getWriteFor())){
            $adapter = $this->_getHelper()->getTemplateAdapterByModel($this);
            $attributes = $adapter->getAttributeCodes();

            foreach($attributes as $attributeCode){
                $collection->addAttributeToSelect($attributeCode);

                $collection->addAttributeToFilter(
                    array(
                        array('attribute' => $attributeCode, 'null' => true),
                        array('attribute' => $attributeCode, 'in' => '')
                    ), null, 'left'
                );
            }
        }

        if(isset($assignItems)){
            if(is_array($assignItems) && count($assignItems)){
                if(is_array($excludeItemIds) && count($excludeItemIds)){
                    $assignItems = array_diff(array_map('intval', $assignItems), array_map('intval', $excludeItemIds));
                    if(!count($assignItems)){
                        return false;
                    }
                }
                $collection->getSelect()->where('e.entity_id IN (?)', $assignItems);
            }else{
                return false;
            }
        }else{
            if(is_array($excludeItemIds) && count($excludeItemIds)){
                $collection->getSelect()->where('e.entity_id NOT IN (?)', $excludeItemIds);
            }
        }

        if($onlyCountFlag){
            return $collection->count();
        }

        $collection->addAttributeToSelect('*');
        $collection->getSelect()->limit($limit, $from);

//            echo "<pre>"; print_r($collection->getItems()); echo "</pre>"; exit;
//            echo $collection->getSelect()->__toString(); exit;
//            echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>"; //exit;
        return $collection;
    }


    /**
     *
     * @return array
     */
    public function getAssignForAnalogTemplateProductIds()
    {
        $collection = $this->getCollection()
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAssignTypeFilter($this->_getHelper()->getAssignForIndividualItems());
        if ($this->getTemplateId()) {
            $collection->excludeTemplateFilter($this->getTemplateId());
        }

        $templateIDs = $collection->getAllIds();

        return $this->getIndividualRelatedModel()->getResource()->getItemIds($templateIDs);
    }


    /**
     *
     * @return array
     */
    public function getAssignForAnalogTemplateAttributesetIds()
    {
        $collection = $this->getCollection()
            ->addSpecificStoreFilter($this->getStoreId())
            ->addTypeFilter($this->getTypeId())
            ->addAssignTypeFilter($this->_getHelper()->getAssignForGroupItems());

        if ($this->getTemplateId()) {
            $collection->excludeTemplateFilter($this->getTemplateId());
        }

        $templateIDs = $collection->getAllIds();
        return $this->getGroupRelatedModel()->getResource()->getItemIds($templateIDs);
    }

    /**
     *
     * @param int $nestedStoreId
     * @return array|false
     */
    protected function _getExcludeItemIdsByTemplate($nestedStoreId = null)
    {
        $templateCollection = $this->getCollection()->addTypeFilter($this->getTypeId());

        if($this->getStoreId() == '0'){

            if($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
                $templateCollection->addSpecificStoreFilter($nestedStoreId);
                $templateCollection->addAssignTypeFilter(array($this->_getHelper()->getAssignForIndividualItems(), $this->_getHelper()->getAssignForGroupItems()));
            }
            elseif($this->_getHelper()->isAssignForGroupItems($this->getAssignType())){
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->addAssignTypeFilter(array($this->_getHelper()->getAssignForIndividualItems(), $this->_getHelper()->getAssignForGroupItems()));
                $templateCollection->excludeTemplateFilter($this->getTemplateId());
            }
            elseif($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $templateCollection->addStoreFilter($nestedStoreId);
                $templateCollection->addAssignTypeFilter(array($this->_getHelper()->getAssignForIndividualItems(), $this->_getHelper()->getAssignForGroupItems()));
            }
        }
        elseif($this->getStoreId()){
            if($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){
                return false;
            }
            elseif($this->_getHelper()->isAssignForGroupItems($this->getAssignType())){
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
                $templateCollection->addAssignTypeFilter($this->_getHelper()->getAssignForIndividualItems());
            }
            elseif($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
                $templateCollection->addSpecificStoreFilter($this->getStoreId());
                $templateCollection->addAssignTypeFilter(array($this->_getHelper()->getAssignForIndividualItems(), $this->_getHelper()->getAssignForGroupItems()));
            }
        }

        $excludeItemIds = array();

        foreach($templateCollection as $template)
        {
            $template->loadItems();
            if(!$this->_getHelper()->isAssignForAllItems($template->getAssignType())){
                $itemIds = $template->getInItems();
                if(is_array($itemIds)){
                    $excludeItemIds = array_merge($excludeItemIds, $itemIds);
                }
            }
        }

        return (!empty($excludeItemIds)) ? $excludeItemIds : false;
    }

    /**
     * Retrine reindex proccesses by templates
     * @param array $nextIds
     * @return array
     */
    public function getReindexProccesses($nextIds)
    {
        $ids = (!is_array($nextIds)) ? explode('-', $nextIds) : $nextIds;

        if($this->getId()){
            array_push($ids, $this->getId());
        }
        $data = $this->getCollection()->loadByIds($ids)->addFieldToSelect('type_id')->toArray();

        $processes = array();
        if(is_array($data['items']) && count($data['items'])){
            foreach($data['items'] as $item){
                switch($item['type_id']){
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_URL_KEY:
                        $processes[] = 'catalog_url';
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_NAME:
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_SHORT_DESCRIPTION:
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_DESCRIPTION:
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_GALLERY:
                        $processes[] = 'catalog_product_attribute';
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_META_TITLE:
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_META_DESCRIPTION:
                    case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_META_KEYWORDS:
                        $processes[] = 'catalog_product_flat';
                        $processes[] = 'catalogsearch_fulltext';
                        break;
                }
            }
        }
        return array_unique($processes);
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template_Product
     */
    protected function _getHelper()
    {
        return Mage::helper("mageworx_seoxtemplates/template_product");
    }
}