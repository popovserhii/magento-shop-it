<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Page_HreflangKey extends Mage_Core_Model_Abstract
{
    /**
     * Add "Hreflang Key" field
     *
     * @param Varien_Event_Observer $observer
     * @return this
     */
    public function addHreflangKeyFormField($observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form      = $observer->getForm();
        $fieldset  = $form->getElements()->searchById('meta_fieldset');

        if (!$fieldset) {
            return $this;
        }

        if (Mage::helper('mageworx_seobase/hreflang')->getCmsPageRelationWay() == MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_IDENTIFIER) {
            $message = Mage::helper('mageworx_seobase')->__('This setting works. You can see other options in <br><i>SEO Suite -> SEO Alternate URLs</i> config section.');
        } else {
            $message = Mage::helper('mageworx_seobase')->__('This setting is disabled. You can enable it in <br><i>SEO Suite -> SEO Alternate URLs</i> config section.');
        }

        $hint = '<p class="note entered">' . $message . '</p>';

        $fieldset->addField('mageworx_hreflang_identifier', 'text',
                array(
                    'name'     => 'mageworx_hreflang_identifier',
                    'label'    => Mage::helper('mageworx_seobase')->__('Hreflang Key'),
                    'title'    => Mage::helper('mageworx_seobase')->__('Hreflang Key'),
                    'required' => false,
                    'disabled' => false,
                    'class' => 'validate-data',
                    'after_element_html' => $hint,
                )
        );

        $model = Mage::registry('cms_page');
        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }
}