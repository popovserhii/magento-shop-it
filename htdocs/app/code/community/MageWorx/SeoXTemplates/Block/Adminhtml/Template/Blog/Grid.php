<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Blog_Grid extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepare template grid columns
     *
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    protected function _prepareColumns()
    {
        return parent::_prepareColumns()->removeColumn('store_id');
    }

}