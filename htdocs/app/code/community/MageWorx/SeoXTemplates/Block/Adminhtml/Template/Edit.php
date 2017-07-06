<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'template_id';
        $this->_blockGroup = 'mageworx_seoxtemplates';

        parent::__construct();

        if (Mage::helper('mageworx_seoxtemplates')->getStep() == 'new_step_1') {

            $this->_removeButton('reset');
            $this->_updateButton('save', '',
                array(
                'label'      => Mage::helper('mageworx_seoxtemplates')->__('Continue Edit'),
                'onclick'    => 'continueEdit()',
                'class'      => 'save',
                'sort_order' => 20
                ), -100);

            $this->_formScripts[] = "
                function continueEdit() {
                    editForm.submit($('edit_form').action + 'prepare/edit');
                }
            ";
        }
        else {
            $this->_removeButton('reset');
            $this->_updateButton('save', '',
                array(
                'label'      => Mage::helper('catalog')->__('Save'),
                'onclick'    => 'saveTemplatesForm()',
                'class'      => 'save',
                'sort_order' => 30
                ), 1);

            $this->_updateButton('delete', '',
                array(
                'label'      => Mage::helper('catalog')->__('Delete'),
                'onclick'    => "deleteConfirm('{$this->__('Are you sure you want to do this?')}', '{$this->getUrl('*/*/delete',
                    array('template_id' => (int) $this->getRequest()->getParam('template_id')))}')",
                'class'      => 'delete',
                'sort_order' => 10
            ));


            $this->_formScripts[] = $this->_getFormScript();
        }
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template
     */
    public function getSeoTemplate()
    {
        return Mage::helper('mageworx_seoxtemplates/factory')->getModel();
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

            return Mage::helper('mageworx_seoxtemplates')->__("Edit %s Template (\"%s\") for \"%s\" Store View", $type,
                    $template->getName(), $storeview);
        }
        elseif (Mage::helper('mageworx_seoxtemplates')->getStep() == 'new_step_2') {
            $type      = Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getTypeByTypeId($this->getRequest()->getParam('type_id'));
            $storeview = $this->_getStoreViewAsString();

            return Mage::helper('mageworx_seoxtemplates')->__("Edit \"%s\" Template for \"%s\" Store View", $type, $storeview);
        }
        else {
            $name = ucfirst(Mage::helper('mageworx_seoxtemplates/factory')->getItemType());
            return Mage::helper('mageworx_seoxtemplates')->__("Create New Template for") . ' ' . $name;
        }
    }

    /**
     * Retrive store name
     * @return string
     */
    protected function _getStoreViewAsString()
    {
        if ($storeId = $this->getRequest()->getParam('store')) {
            $storeview = Mage::app()->getStore($storeId)->getName();
        }
        else {
            $storeview = Mage::helper('core')->__('Default');
        }
        return $storeview;
    }
}
