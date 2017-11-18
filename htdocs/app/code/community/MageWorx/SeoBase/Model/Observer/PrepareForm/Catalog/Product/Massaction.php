<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PrepareForm_Catalog_Product_Massaction
{
    /**
     * Add to excluded field list "canonical_url" attribute
     * Add source modules for "meta_robots" and "canonical_cross_domain" attributes
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function modifyAttributes($observer)
    {
        $object = $observer->getObject();

        $excludeAttributes = $object->getFormExcludedFieldList();
        $excludeAttributes[] = 'canonical_url';
        $object->setFormExcludedFieldList($excludeAttributes);

        foreach ($object->getAttributes() as $attribute) {
            if ($attribute->getAttributeCode() == 'meta_robots') {
                $attribute->setSourceModel('mageworx_seobase/catalog_product_attribute_source_meta_robots');
            }

            if ($attribute->getAttributeCode() == 'canonical_cross_domain') {
                $attribute->setSourceModel('mageworx_seobase/system_config_source_crossdomain');
            }
        }
    }
}