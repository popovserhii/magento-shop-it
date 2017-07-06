<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Product_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $model = Mage::registry('current_redirect_instance');

        $data = (is_object($model) && count($model->getData())) ? $model->getData() : $this->_getDefaultData();

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('mageworx_seoredirects')->__('SEO Product Redirect Settings')));

        $fieldset->addField(
            'redirect_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Redirect ID'),
                'name'     => 'redirect_id',
                'index'    => 'redirect_id',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'product_id',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Product ID'),
                'name'     => 'product_id',
                'index'    => 'product_id',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'product_name',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Product Name'),
                'name'     => 'request_path',
                'index'    => 'request_path',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'request_path',
            'text',
            array(
                'label'    => Mage::helper('adminhtml')->__('Request Path'),
                'name'     => 'request_path',
                'index'    => 'request_path',
                'disabled' => true
            )
        );

        $categoryOptionArray = Mage::getSingleton('mageworx_seoredirects/source_category')->toOptionArray();

        if ($this->_isInvalidCategoryId($data['category_id'], $categoryOptionArray)) {
            $categoryNote = $this->_getInvalidCategoryNote($data['category_id']);
        }

        $fieldset->addField(
            'category_id',
            'select',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Product Category'),
                'name'     => 'category_id',
                'index'    => 'category_id',
                'values'   => $categoryOptionArray,
                'note'     => !empty($categoryNote) ? $categoryNote : '',
                'required' => true
            )
        );


        if ($this->_isInvalidCategoryId($data['category_id'], $categoryOptionArray)) {
            $priorityCategoryNote = $this->_getInvalidCategoryNote($data['category_id']);
        }

        $fieldset->addField(
            'priority_category_id',
            'select',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Priority Category'),
                'name'     => 'priority_category_id',
                'index'    => 'priority_category_id',
                'values'   => $categoryOptionArray,
                'note'     => !empty($priorityCategoryNote) ? $priorityCategoryNote : '',
                'required' => true
            )
        );

        $fieldset->addField(
            'hits',
            'text',
            array(
                'label'    => Mage::helper('mageworx_seoredirects')->__('Hits'),
                'name'     => 'hits',
                'index'    => 'hits',
                'class'    => 'required-entry validate-not-negative-number',
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('catalog')->__('Enabled'),
                'name'   => 'status',
                'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
            )
        );

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve array of default redirects data for current tab
     *
     * @return array
     */
    protected function _getDefaultData()
    {
        return array();
    }

    /**
     *
     * @param int $categoryId
     * @return boolean
     */
    protected function _isInvalidCategoryId($categoryId)
    {
        return !array_key_exists($categoryId, Mage::getSingleton('mageworx_seoredirects/source_category')->toOptionArray());
    }

    /**
     *
     * @param int $categoryId
     * @return string
     */
    protected function _getInvalidCategoryNote($categoryId)
    {
        return Mage::helper('mageworx_seoredirects')->__('Last request category ID %s is invalid', $categoryId);
    }
}
