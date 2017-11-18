<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Template extends Mage_Core_Model_Abstract
{
    abstract protected function _getHelper();

    /**
     * Retrive duplicate template that is assigned to all items
     *
     * @return MageWorx_SeoXTemplates_Model_Template|false
     */
    public function getAllTypeDuplicateTemplate()
    {
        $templateCollection = $this->getCollection()
            ->addSpecificStoreFilter($this->getStoreId())
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
     * Check if the template that is assigned to all items already exists
     *
     * @param int $nestedStoreId
     * @return boolean
     */
    protected function _issetUniqStoreTemplateForAllItems($nestedStoreId)
    {
        if ($this->getStoreId() == '0') {
            $templateCollection = $this->getCollection()
                ->addTypeFilter($this->getTypeId())
                ->addAssignTypeFilter($this->_getHelper()->getAssignForAllItems())
                ->addSpecificStoreFilter($nestedStoreId);

            if($templateCollection->count()){
                return true;
            }
        }
        return false;
    }
}
