<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Block_Adminhtml_Crosslink_Edit_Tab_Destination extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return this
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset(
            'destination',
            array('legend' => Mage::helper('mageworx_seocrosslinks')->__('Create Destinations'))
        );

        $model = Mage::registry('current_crosslink_instance');
        $data = (is_object($model) && count($model->getData())) ? $model->getData() : $this->_getDefaultData();

        $fieldset->addField(
            'in_product',
            'select',
            array(
                'label'  => Mage::helper('mageworx_seocrosslinks')->__('Use for Product Page'),
                'name'   => 'in_product',
                'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
            )
        );

        $fieldset->addField(
            'in_category',
            'select',
            array(
                'label'  => Mage::helper('catalog')->__('Use in Category Page'),
                'name'   => 'in_category',
                'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
            )
        );

        $fieldset->addField(
            'in_cms_page',
            'select',
            array(
                'label'  => Mage::helper('catalog')->__('Use in CMS Page'),
                'name'   => 'in_cms_page',
                'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
            )
        );

        $fieldset->addField(
            'in_blog',
            'select',
            array(
                'label'  => Mage::helper('catalog')->__('Use in Blog Post Page'),
                'name'   => 'in_blog',
                'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
            )
        );

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }


    /**
     * Retrive array of default cross link destination data for current tab
     *
     * @return array
     */
    protected function _getDefaultData()
    {
        return array(
            'in_product'       => Mage::helper('mageworx_seocrosslinks')->getDefaultForProductPage(),
            'in_category'      => Mage::helper('mageworx_seocrosslinks')->getDefaultForCategoryPage(),
            'in_cms_page'      => Mage::helper('mageworx_seocrosslinks')->getDefaultForCmsPageContent(),
            'in_blog'          => Mage::helper('mageworx_seocrosslinks')->getDefaultForBlogContent(),
        );
    }
}
