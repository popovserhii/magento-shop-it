<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Attribute extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeCode
     * @return array|string
     */
    public function getAttributeValueByCode($product, $attributeCode)
    {
        $tempValue = '';
        $value     = $product->getData($attributeCode);
        if ($_attr     = $product->getResource()->getAttribute($attributeCode)) {
            $_attr->setStoreId($product->getStoreId());
            if ($_attr->usesSource()) {
                $tempValue = $_attr->setStoreId($product->getStoreId())->getSource()->getOptionText($product->getData($attributeCode));
            }
        }
        if ($tempValue) {
            $value = $tempValue;
        }

        if (!$value) {
            if ($product->getTypeId() == 'configurable') {
                $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                $attributeOptions        = array();
                foreach ($productAttributeOptions as $productAttribute) {
                    if ($productAttribute['attribute_code'] == $attributeCode) {
                        foreach ($productAttribute['values'] as $attribute) {
                            $attributeOptions[] = $attribute['store_label'];
                        }
                    }
                }
                if (count($attributeOptions) == 1) {
                    $value = array_shift($attributeOptions);
                }
            }
            else {
                $value = $product->getData($attributeCode);
            }
        }

        if(is_array($value)) {
            $finalValue = array_map('trim', array_filter($value));
        }else {
            $finalValue = trim($value);
        }

        return $finalValue;
    }
}