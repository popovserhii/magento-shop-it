<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Catalog_Robots
{
    /**
     * Retrive options for Meta Robots attribute
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function modifyFormField($observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        $form = $observer->getForm();
        $metaRobots = $form->getElement('meta_robots');

        if ($metaRobots) {
            $metaRobots->setValues(
                Mage::getModel('mageworx_seobase/catalog_product_attribute_source_meta_robots')->getAllOptions()
            );
        }
    }
}