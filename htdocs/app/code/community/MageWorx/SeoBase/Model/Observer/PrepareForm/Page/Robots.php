<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Page_Robots extends Mage_Core_Model_Abstract
{
    /**
     * Add "Meta Robots" field
     *
     * @param Varien_Event_Observer $observer
     * @return this
     */
    public function addRobotsFormField($observer)
    {
        //adminhtml_cms_page_edit_tab_meta_prepare_form
        $form      = $observer->getForm();
        $fieldset  = $form->getElements()->searchById('meta_fieldset');

        if (!$fieldset) {
            return $this;
        }

        if (!$form->getElement('meta_robots')) {

            $metaRobotValues = Mage::getModel('mageworx_seobase/catalog_product_attribute_source_meta_robots')->getAllOptions();

			$fieldset->addField('meta_robots', 'select',
				array(
					'name'   => 'meta_robots',
					'label'  => __('Meta Robots'),
					'title'  => __('Meta Robots'),
					'values' => $metaRobotValues,
					'note'   => __('This setting was added by MageWorx SEO Suite')
				)
			);
        }

        return $this;
    }
}