<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        if ($template = Mage::helper('mageworx_seoxtemplates/factory')->getModel()) {

            $data    = $template->getData();
            if ($storeId = $this->getRequest()->getParam('store')) {
                $data['store_id'] = $storeId;
            }
            if ($typeId = $this->getRequest()->getParam('type_id')) {
                $data['type_id'] = $typeId;
            }
        }

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('customer')->__('Template Information')));

        $fieldset->addField('name', 'text',
            array(
            'label'    => Mage::helper('catalog')->__('Name'),
            'name'     => 'general[name]',
            'index'    => 'name',
            'required' => true
        ));

        $fieldset->addField('type_id', 'hidden',
            array(
            'name'   => 'general[type_id]',
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreOptionHash(false),
        ));

        if (!Mage::app()->isSingleStoreMode()) {

            $fieldset->addField('store_id', 'hidden',
                array(
                'name'   => 'general[store_id]',
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreOptionHash(false),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden',
                array(
                'name'  => 'general[store_id]',
                'value' => Mage::app()->getStore(true)->getId(),
            ));
        }

        $templateCode = $fieldset->addField('code', 'text',
            array(
            'name'     => 'general[code]',
            'label'    => Mage::helper('mageworx_seoxtemplates')->__('Template Rule'),
            'title'    => Mage::helper('mageworx_seoxtemplates')->__('Template Rule'),
            'style'   => "width:100%",
            'required' => true,
        ));

        $templateCode->setAfterElementHtml("<div style='width:1000px'>" .
            Mage::helper('mageworx_seoxtemplates/factory')->getCommentHelper()->getComment($data['type_id']) . "</div>");

        $fieldset->addField('write_for', 'select',
            array(
            'label'  => Mage::helper('mageworx_seoxtemplates')->__('Apply For'),
            'name'   => 'general[write_for]',
            'index'  => 'write_for',
            'values' => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getWriteForArray()
        ));

        $useCron = $fieldset->addField('use_cron', 'select',
            array(
            'label'  => Mage::helper('mageworx_seoxtemplates')->__('Apply By Cron'),
            'name'   => 'general[use_cron]',
            'index'  => 'use_cron',
            'values' => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getUseCronArray(),
        ));

        $cronNoticeMessage = '<br>' . $this->__("Note: Do not enable this option if you use the template as a template variable,"
            . " it may lead to duplication.");

        $cronNoticeMessage .= '<br>' . $this->__("For example, if your Product Meta Title template is: '[meta_title]-Some_Text',"
            . " after the 3rd application, your meta titles will turn into: 'meta_title-Some_Text-Some_Text-Some_Text'.");

        $useCron->setAfterElementHtml('<p><font color="#ea7601">' . $cronNoticeMessage . '</font></p>');

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
