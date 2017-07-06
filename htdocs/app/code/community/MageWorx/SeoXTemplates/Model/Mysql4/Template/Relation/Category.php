<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Category extends MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation
{
    /**
     * Define main table
     *
     */
    public function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_relation_category', 'id');
    }

    /**
     * Retrive item ids by template(s)
     * @param int|array $templateId
     * @return array
     */
    public function getItemIds($templateId)
    {
        if (!is_array($templateId)) {
            $templateId = array($templateId);
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr("DISTINCT `category_id`"))
            ->where('template_id IN (?)', $templateId);

        $result = array();
        $data   = $this->_getReadAdapter()->fetchAssoc($select);
        if ($data && is_array($data)) {
            $result = array_keys($data);
        }
        return $result;
    }
}