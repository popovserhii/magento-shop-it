<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Mysql4_Template_Category extends MageWorx_SeoXTemplates_Model_Mysql4_Template
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_category', 'template_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->loadItems();
        return parent::_afterLoad($object);
    }

    /**
     * Calc priority for template
     *
     * @param MageWorx_SeoXTemplates_Model_Template_Category $object
     * @return int
     */
    protected function _calcPriority($object)
    {
        if ($object->getStoreId() == 0) {
            if ($this->_getHelper()->isAssignForAllItems($object->getAssignType())) {
                $priority = 1;
            }
            elseif ($this->_getHelper()->isAssignForIndividualItems($object->getAssignType())) {
                $priority = 2;
            }
        }
        else {
            if ($this->_getHelper()->isAssignForAllItems($object->getAssignType())) {
                $priority = 3;
            }
            elseif ($this->_getHelper()->isAssignForIndividualItems($object->getAssignType())) {
                $priority = 4;
            }
        }
        return $priority;
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Category
     */
    protected function _getIndividualRelatedResourceModel()
    {
        return Mage::getResourceSingleton("mageworx_seoxtemplates/template_relation_category");
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template_Category
     */
    protected function _getHelper()
    {
        return Mage::helper('mageworx_seoxtemplates/template_category');
    }
}