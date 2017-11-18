<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Attribute_LayeredNavigationCanonical
{

    /**
     * Add "Canonical Tag for Pages Filtered by Layered Navigation Leads to" field for product attributes
     *
     * @param  Varien_Event_Observer $observer
     * @return this
     */
    public function modifyFormField(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('mageworx_seobase');
        $form   = $observer->getEvent()->getForm();

        $fieldset = $form->getElements()->searchById('front_fieldset');
        if (!is_null($fieldset)) {
            $fieldset->addField('layered_navigation_canonical', 'select',
                    array(
                'name'   => 'layered_navigation_canonical',
                'label'  => $helper->__('Canonical Tag for Pages Filtered by Layered Navigation Leads to'),
                'title'  => $helper->__('Canonical Tag for Pages Filtered by Layered Navigation Leads to'),
                'values' => Mage::getModel('mageworx_seobase/system_config_source_layer_canonical')->toOptionArray(),
                    ), 'is_filterable_in_search');
        }
        
        return $this;
    }
}