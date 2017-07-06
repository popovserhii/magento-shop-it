<?php

/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_HideEmptyAttributes
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
//class ET_HideEmptyAttributes_Block_Default extends Mage_Catalog_Block_Product_View_Attributes
class ET_HideEmptyAttributes_Block_Default extends ET_HideEmptyAttributes_Block_Product_View_Attributes_RewriteDetector
{

    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if ($attribute->getFrontendInput() == 'price' && is_string($value)) {

                    $value = Mage::app()->getStore()->convertPrice($value, true);
                    // hasData for empty select values returns true. Using getAttributeText instead
                } else if ($attribute->getFrontendInput() == 'select'
                    && !$product->getAttributeText($attribute->getAttributeCode())
                ) {
                    $value = "";
                } else if (Mage::helper('ethideemptyattributes')->ifEnabledHideBooleanNo()
                    && $attribute->getFrontendInput() == 'boolean'
                    && !$product->getData($attribute->getAttributeCode())
                ) {
                    $value = "";
                }

                if (is_string($value) && strlen(trim($value))) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode()
                    );
                }
            }
        }
        return $data;


    }
}