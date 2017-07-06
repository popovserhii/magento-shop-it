<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Page_MetaTitle extends Mage_Core_Model_Abstract
{
    /**
     * Add "Meta Title" field
     *
     * @param Varien_Event_Observer $observer
     * @return this
     */
    public function addMetaTitleFormField($observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form      = $observer->getForm();
        $fieldset  = $form->getElements()->searchById('meta_fieldset');

        if (!$fieldset) {
            return $this;
        }

        if (!$form->getElement('meta_title')) {

            $fieldset->addField('meta_title', 'text',
                    array(
                        'name'     => 'meta_title',
                        'label'    => Mage::helper('cms')->__('Title'),
                        'title'    => Mage::helper('cms')->__('Title'),
                        'required' => false,
                        'disabled' => false
                    ), '^'
            );
        }

        return $this;
    }
}