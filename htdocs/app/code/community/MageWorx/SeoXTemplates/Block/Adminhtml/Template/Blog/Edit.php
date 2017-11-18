<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Blog_Edit extends MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_template_blog';
        parent::__construct();
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $template = $this->getSeoTemplate();

        if ($storeId = $this->getRequest()->getParam('store')) {
            $storeview = Mage::app()->getStore($storeId)->getName();
        }
        else {
            $storeview = Mage::helper('core')->__('Default');
        }

        if (Mage::helper('mageworx_seoxtemplates')->getStep() == 'edit') {
            $type      = Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getTypeByTypeId($template->getTypeId());
            $storeview = $this->_getStoreViewAsString();

            return Mage::helper('mageworx_seoxtemplates')->__("Edit %s Template (\"%s\")", $type,
                    $template->getName(), $storeview);
        }
        elseif (Mage::helper('mageworx_seoxtemplates')->getStep() == 'new_step_2') {
            $type      = Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getTypeByTypeId($this->getRequest()->getParam('type_id'));
            $storeview = $this->_getStoreViewAsString();

            return Mage::helper('mageworx_seoxtemplates')->__("Edit \"%s\" Template", $type, $storeview);
        }
        else {
            $name = ucfirst(Mage::helper('mageworx_seoxtemplates/factory')->getItemType());
            return Mage::helper('mageworx_seoxtemplates')->__("Create New Template for") . ' ' . $name;
        }
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
