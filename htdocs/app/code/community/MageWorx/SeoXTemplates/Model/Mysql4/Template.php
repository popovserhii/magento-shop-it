<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Mysql4_Template extends Mage_Core_Model_Mysql4_Abstract
{
    abstract protected function _getIndividualRelatedResourceModel();

    /**
     * Process template before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setPriority($this->_calcPriority($object));
        return parent::_beforeSave($object);
    }

    /**
     * Process template before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $this->_getIndividualRelatedResourceModel()->deleteTemplateItemRelation($object->getId());
        return parent::_beforeDelete($object);
    }
}