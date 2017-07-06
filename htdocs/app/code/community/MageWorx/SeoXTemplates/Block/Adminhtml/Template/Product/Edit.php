<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Product_Edit extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_template_product';
        parent::__construct();
    }

    /**
     * Retrive JS code for save action
     * @return string
     */
    protected function _getFormScript()
    {
        return
        "
           function saveTemplatesForm() {
                applySelectedProducts('save')
            }
        ";
    }

}
