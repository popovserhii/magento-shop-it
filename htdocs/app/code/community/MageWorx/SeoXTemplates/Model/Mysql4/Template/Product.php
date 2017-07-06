<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Mysql4_Template_Product extends MageWorx_SeoXTemplates_Model_Mysql4_Template
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_product', 'template_id');
    }

    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $object->loadItems();
        return parent::_afterLoad($object);
    }

    /**
     * Calc priority for template
     *
     * @param MageWorx_SeoXTemplates_Model_Template_Product $object
     * @return int
     */
    protected function _calcPriority($object)
    {
        if ($object->getStoreId() == 0) {
            if ($this->_getHelper()->isAssignForAllItems($object->getAssignType())) {
                $priority = 1;
            }
            elseif ($this->_getHelper()->isAssignForGroupItems($object->getAssignType())) {
                $priority = 2;
            }
            elseif ($this->_getHelper()->isAssignForIndividualItems($object->getAssignType())) {
                $priority = 3;
            }
        }
        else {
            if ($this->_getHelper()->isAssignForAllItems($object->getAssignType())) {
                $priority = 4;
            }
            elseif ($this->_getHelper()->isAssignForGroupItems($object->getAssignType())) {
                $priority = 5;
            }
            elseif ($this->_getHelper()->isAssignForIndividualItems($object->getAssignType())) {
                $priority = 6;
            }
        }
        return $priority;
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Product
     */
    protected function _getIndividualRelatedResourceModel()
    {
        return Mage::getResourceSingleton("mageworx_seoxtemplates/template_relation_product");
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template_Product
     */
    protected function _getHelper()
    {
        return Mage::helper('mageworx_seoxtemplates/template_product');
    }
}