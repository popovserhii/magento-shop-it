<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Category_Collection extends MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Collection
{
    /**
     * Initialize resource model
     *
     */
    public function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_relation_category');
    }

    /**
     *
     * @param int $itemId
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Relation_Category_Collection
     */
    public function loadByItemId($itemId)
    {
        $this->getSelect()->where("category_id = ?", $itemId);
        return $this;
    }

}