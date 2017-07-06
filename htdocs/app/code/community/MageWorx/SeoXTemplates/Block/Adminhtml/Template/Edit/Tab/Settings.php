<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tab_Settings
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('catalog')->__('Create Template Settings')));

        $fieldset->addField('type_id', 'select',
            array(
            'label'  => Mage::helper('catalog')->__('Type'),
            'index'   => 'type_id',
            'name' => 'general[type_id]',
            'required' => true,
            'options' => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getAllTypeOptions(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
           $field = $fieldset->addField('store_id', 'select', array(
                'name'      => 'general[store_id]',
                'label'     => Mage::helper('catalog')->__('Store View'),
                'title'     => Mage::helper('catalog')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'general[store_id]',
                'value' => Mage::app()->getStore(true)->getId(),
            ));
        }

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
