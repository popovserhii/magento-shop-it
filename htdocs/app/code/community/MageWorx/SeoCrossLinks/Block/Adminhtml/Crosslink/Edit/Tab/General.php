<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Block_Adminhtml_Crosslink_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $model = Mage::registry('current_crosslink_instance');

        $data = (is_object($model) && count($model->getData())) ? $model->getData() : $this->_getDefaultData();

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('customer')->__('Cross Link Settings')));

        $kNote1 = $this->__("NOTE: Enter one keyword (keyword phrase) per line. "
            . "A new cross link rule will be created for each entered keyword.");

        $kNote2 = $this->__("&nbsp;&nbsp;&nbsp;&nbsp;For multiple keywords use the Reduced Multisave Priority feature."
            . " It reduces the keyword priority for every next keyword on the list "
            . "(thus, the most important keywords appear in the first place).");

        $kNote3 = $this->__("&nbsp;&nbsp;&nbsp;&nbsp;Adding '+' before or after a keyword will apply the Cross Link rule to all its variations. "
            . "E.g. Entering 'iphone 5+' will apply the rule to 'iphone 5s', 'iphone 5c', etc. (but not to 'iphone 5').");


        $hrefBefore = '<a href="http://support.mageworx.com/index.php?/Knowledgebase/Article/View/76/4/how-to-add-multiple-keywords-for-creating-internal-links" target="_blank">';
        $hrefAfter  = '</a>';
        $kInfo = $this->__('&nbsp;&nbsp;&nbsp;&nbsp;For more info, follow the %s link %s.', $hrefBefore, $hrefAfter);

        $fieldset->addField(
            'keyword',
            'textarea',
            array(
                'label'    => Mage::helper('catalog')->__('Keyword'),
                'name'     => 'keyword',
                'index'    => 'keyword',
                'required' => true,
                'note'     => $this->__($kNote1 . '<br>' . $kNote2 . '<br>' . $kNote3 . '<br>' . $kInfo)
            )
        );

        $fieldset->addField(
            'link_title',
            'text',
            array(
                'label'    => Mage::helper('catalog')->__('Link Title'),
                'name'     => 'link_title',
                'index'    => 'link_title',
            )
        );

        $fieldset->addField(
            'link_target',
            'select',
            array(
                'label'    => Mage::helper('catalog')->__('Link Target'),
                'name'     => 'link_target',
                'index'    => 'link_target',
                'values'   => Mage::getSingleton('mageworx_seocrosslinks/crosslink')->getTargetLinkDescriptionArray()
            )
        );

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'note'      =>__('NOTE: Cross Link will be build in the chosen store views.')
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $reference = $fieldset->addField(
            'reference',
            'select',
            array(
                'label'     => Mage::helper('catalog')->__('Reference'),
                'name'      => 'reference',
                'values'    => Mage::getModel('mageworx_seocrosslinks/source_reference')->toOptionArray()
            )
        );

        $staticUrlNote0 = Mage::helper('mageworx_seocrosslinks')->__("Link without 'http[s]://' as customer/account/<br>will be converted to<br>http[s]://(store_URL_here)/customer/account/");
        $staticUrlNote1 = Mage::helper('mageworx_seocrosslinks')->__("Link with 'http[s]://' will be added as it is.");

        $url = $fieldset->addField(
            'ref_static_url',
            'text',
            array(
                'label'    => Mage::helper('catalog')->__('Custom URL'),
                'name'     => 'ref_static_url',
                'index'    => 'ref_static_url',
                'note'     => $staticUrlNote0 . "<br>" . $staticUrlNote1,
                'class'    => 'required-entry',
                'required' => true
            )
        );

        $product = $fieldset->addField(
            'ref_product_sku',
            'text',
            array(
                'label'    => Mage::helper('catalog')->__('Product SKU'),
                'name'     => 'ref_product_sku',
                'index'    => 'ref_product_sku',
                'required' => true
            )
        );

        $category = $fieldset->addField(
            'ref_category_id',
            'text',
            array(
                'label'    => Mage::helper('catalog')->__('Category ID'),
                'name'     => 'ref_category_id',
                'index'    => 'ref_category_id',
                'required' => true
            )
        );

        $fieldset->addField(
            'replacement_count',
            'text',
            array(
                'label'    => Mage::helper('catalog')->__('Max Replacement Count per Page'),
                'name'     => 'replacement_count',
                'index'    => 'replacement_count',
                'class'    => 'required-entry validate-percents',
                'note'     => $this->__('Max # of this keyword per page')
            )
        );

        $fieldset->addField(
            'priority',
            'text',
            array(
                'label'    => Mage::helper('catalog')->__('Priority'),
                'name'     => 'priority',
                'index'    => 'priority',
                'class'    => 'required-entry validate-percents',
                'note'     => $this->__('100 is the highest priority.')
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('catalog')->__('Enabled'),
                'name'   => 'status',
                'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
                'note'   => $this->__('If enabled, the cross link will be built on the fly.')
            )
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($reference->getHtmlId(), $reference->getName())
            ->addFieldMap($url->getHtmlId(), $url->getName())
            ->addFieldMap($product->getHtmlId(), $product->getName())
            ->addFieldMap($category->getHtmlId(), $category->getName())
            ->addFieldDependence(
                $url->getName(),
                $reference->getName(),
                'ref_static_url'
            )
            ->addFieldDependence(
                $product->getName(),
                $reference->getName(),
                'ref_product_sku'
            )
            ->addFieldDependence(
                $category->getName(),
                $reference->getName(),
                'ref_category_id'
            )
        );

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrive array of default cross link data for current tab
     *
     * @return array
     */
    protected function _getDefaultData()
    {
        $helperData = Mage::helper('mageworx_seocrosslinks');

        return array(
            'reference'         => $helperData->getDefaultReference(),
            'replacement_count' => $helperData->getDefaultReplacementCount(),
            'priority'          => $helperData->getDefaultPriority(),
            'status'            => $helperData->getDefaultStatus()
        );
    }
}
