<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Template_Blog extends MageWorx_SeoXTemplates_Model_Template
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_blog');
        $this->setIdFieldName('template_id');
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template_Relation_Blog
     */
    public function getIndividualRelatedModel()
    {
        return Mage::getSingleton('mageworx_seoxtemplates/template_relation_blog');
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
     * Retrive duplicate template that is assigned to all items
     *
     * @return MageWorx_SeoXTemplates_Model_Template|false
     */
    public function getAllTypeDuplicateTemplate()
    {
        $templateCollection = $this->getCollection()
            ->addTypeFilter($this->getTypeId())
            ->addAssignTypeFilter($this->_getHelper()->getAssignForAllItems());
        if($this->getTemplateId()){
            $templateCollection->excludeTemplateFilter($this->getTemplateId());
        }

        if ($templateCollection->count()) {
            return $templateCollection->getFirstItem();
        }
        return false;
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

        if($this->_getHelper()->isAssignForAllItems($this->getAssignType())){
            $excludeItemIds = $this->_getExcludeItemIdsByTemplate();
        }else{
            $excludeItemIds = false;
        }
        $collection = Mage::getResourceModel('mageworx_seoxtemplates/blog_collection')
            ->joinCategories();

        if($this->_getHelper()->isWriteForEmpty($this->getWriteFor())){
            $adapter = $this->_getHelper()->getTemplateAdapterByModel($this);
            $propertyNames = $adapter->getAttributeCodes();

            foreach($propertyNames as $propertyName){
                $collection->getSelect()->where("main_table.{$propertyName}=''", '');
            }
        }
        if ($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())) {
            $assignItems = (is_array($this->getInItems()) && count($this->getInItems())) ? $this->getInItems() : 0;
            $collection->getSelect()->where('main_table.post_id IN (?)', $assignItems);
        }
        if (!empty($excludeItemIds)) {
            $collection->getSelect()->where('main_table.post_id NOT IN (?)', $excludeItemIds);
        }

        if ($onlyCountFlag) {
            return $collection->count();
        }
        else {
            $collection->getSelect()->limit($limit, $from);

//            echo $collection->getSelect()->__toString(); exit;
//            echo "<pre>"; print_r($collection->getItems()); echo "</pre>"; exit;
            return $collection;
        }
    }

    /**
     *
     * @return array
     */
    public function getAssignForAnalogTemplateBlogIds()
    {
        $collection = $this->getCollection()
            //->addSpecificStoreFilter($this->getStoreId())
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
     * @param int $nestedStoreId
     * @return array|false
     */
    protected function _getExcludeItemIdsByTemplate()
    {
        $templateCollection = $this->getCollection();
        $templateCollection->addTypeFilter($this->getTypeId());
        $templateCollection->addAssignTypeFilter($this->_getHelper()->getAssignForIndividualItems());


        if($this->_getHelper()->isAssignForAllItems($this->getAssignType())){

        }
        elseif($this->_getHelper()->isAssignForIndividualItems($this->getAssignType())){

            $templateCollection->excludeTemplateFilter($this->getTemplateId());
        }


        $templateIds = $templateCollection->getAllIds();

        $excludeItemIds = Mage::getModel("mageworx_seoxtemplates/template_relation_blog")->getResource()->getItemIds($templateIds);
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
                        case MageWorx_SeoXTemplates_Helper_Template_Blog::BLOG_TITLE:
                        case MageWorx_SeoXTemplates_Helper_Template_Blog::BLOG_META_DESCRIPTION:
                        case MageWorx_SeoXTemplates_Helper_Template_Blog::BLOG_META_KEYWORDS:
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
     * @return MageWorx_SeoXTemplates_Helper_Template_Blog
     */
    protected function _getHelper()
    {
        return Mage::helper("mageworx_seoxtemplates/template_blog");
    }

}