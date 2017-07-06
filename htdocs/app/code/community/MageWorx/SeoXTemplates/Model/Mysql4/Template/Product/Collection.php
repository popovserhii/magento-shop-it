<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Mysql4_Template_Product_Collection extends MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seoxtemplates/template_product');
    }

}