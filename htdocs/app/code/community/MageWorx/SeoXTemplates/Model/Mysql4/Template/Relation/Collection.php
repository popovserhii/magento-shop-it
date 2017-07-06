<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Load by template id
     *
     * @param int $templateId
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Collection
     */
    public function loadByTemplateId($templateId)
    {
        $this->getSelect()
            ->where("template_id=?", $templateId);

        return $this;
    }

    /**
     * Load by template ids
     *
     * @param array $templateIds
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Collection
     */
    public function loadByTemplateIds($templateIds = array())
    {
        if (count($templateIds) > 0) {
            $this->getSelect()
                ->where("template_id IN(?)", $templateIds);
        }
        return $this;
    }


    /**
     * Join Template to collection
     *
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Collection
     */
    public function joinTemplates()
    {
        $this->getSelect()
            ->joinLeft(array('templates' => $this->getTable('template')), 'main_table.template_id=templates.template_id');
        return $this;
    }

}