<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Catalog_CrossDomainStore
{
    /**
     * Retrive options for Cross Domain URLs attribute
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function modifyFormField($observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getForm();
        $canonicalCrossDomain = $form->getElement('canonical_cross_domain');

        if ($canonicalCrossDomain) {
            $canonicalCrossDomain->setValues(
                Mage::getModel('mageworx_seobase/system_config_source_crossdomain')->getAllOptions()
            );
        }
    }
}