<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Observer_PrepareForm_Page_Exclude extends Mage_Core_Model_Abstract
{
    /**
     * Add "Exclude from CrossLinking" field
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addCrosslinkExcludeField($observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form      = $observer->getForm();
        $fieldset  = $form->getElements()->searchById('meta_fieldset');

        if (!$fieldset) {
            return $this;
        }

        $message = Mage::helper('mageworx_seocrosslinks')->__('This setting was added by MageWorx SEO CrossLinking');
        $hint    = '<p class="note entered">' . $message . '</p>';

        $fieldset->addField('exclude_from_crosslinking', 'select',
                array(
                    'name'     => 'exclude_from_crosslinking',
                    'label'    => Mage::helper('mageworx_seocrosslinks')->__('Exclude from CrossLinking'),
                    'title'    => Mage::helper('mageworx_seocrosslinks')->__('Exclude from CrossLinking'),
                    'values'   => Mage::getModel('eav/entity_attribute_source_boolean')->getAllOptions(),
                    'after_element_html' => $hint,
                )
        );

        $model = Mage::registry('cms_page');
        $form->setValues($model->getData());
        $this->setForm($form);

        return $this;
    }
}