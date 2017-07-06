<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Catalog_Product_CanonicalUrl
{

    /**
     * Retrive options for canonical URL attribute
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function modifyFormField(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getForm();
        $product = Mage::registry('current_product');

        if ($product && !$product->getId()) {
            foreach ($form->getElements() as $fieldset) {
                $fieldset->removeField('canonical_url');
            }
        }

        $canonical = $form->getElement('canonical_url');

        if ($canonical) {

            if (!Mage::app()->getRequest()->getParam('store') && !Mage::app()->isSingleStoreMode()) {
                $canonical->setRenderer(
                    Mage::app()->getLayout()->createBlock('mageworx_seobase/adminhtml_renderer_canonical')
                );
            } else {
                $canonical->setValues(
                    Mage::getModel('mageworx_seobase/catalog_product_attribute_source_meta_canonical')->getAllOptions()
                );

                $html = "
                    <div style='padding-top:5px;'>
                        <input type='text' value='' style='display:none; width:275px' name='canonical_url_custom' id='canonical_url_custom'>
                    </div>\n
                <script type='text/javascript'>
                function listenCU() {
                    if ($('canonical_url').value=='custom') {
                        $('canonical_url_custom').show();
                    }
                    else {
                        $('canonical_url_custom').hide();
                    }
                }
                $('canonical_url').observe('change',listenCU);
                     </script>";

                $canonical->setAfterElementHtml($html);
            }
        }
    }
}