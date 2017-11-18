<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Model_Observer_Page extends Mage_Core_Model_Abstract
{
    /**
     * Add "Exclude from XML Sitemap" field
     *
     * @param Varien_Event_Observer $observer
     * @return this
     */
    public function addFormFieldsForCmsPage($observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form      = $observer->getForm();
        $fieldset  = $form->getElements()->searchById('meta_fieldset');

        if(!$fieldset){
            return $this;
        }

        $values = Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions();
        $fieldset->addField('exclude_from_sitemap', 'select',
            array(
            'name'   => 'exclude_from_sitemap',
            'label'  => Mage::helper('xsitemap')->__('Exclude from XML Sitemap'),
            'values' => $values,
        ));

        $model = Mage::registry('cms_page');
        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }
}