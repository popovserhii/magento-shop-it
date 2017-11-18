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
class ET_HideEmptyAttributes_Block_Compare extends Mage_Catalog_Block_Product_Compare_List
{

    public function getProductAttributeValue($product, $attribute)
    {
        /** @var $helper ET_HideEmptyAttributes_Helper_Data */
        $helper = Mage::helper('ethideemptyattributes');

        if (!$product->hasData($attribute->getAttributeCode())) {
            return $helper->getEmptyAttributeText();
        }

        if ($attribute->getSourceModel()
            || in_array($attribute->getFrontendInput(), array('select', 'boolean', 'multiselect'))
        ) {
            if (!$product->getAttributeText($attribute->getAttributeCode())) {
                $value = "";
            } else {
                $value = $attribute->getFrontend()->getValue($product);
            }
        } else {
            $value = $product->getData($attribute->getAttributeCode());
        }

        return ((string)trim($value) == '') ? $helper->getEmptyAttributeText() : $value;
    }
}