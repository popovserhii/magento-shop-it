<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     * @param int $templateId
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation
     */
    public function deleteTemplateItemRelation($templateId)
    {
        $this->_getReadAdapter()->delete(
            $this->getMainTable(), $this->_getReadAdapter()->quoteInto('template_id = ?', $templateId, 'INTEGER')
        );

        return $this;
    }

}