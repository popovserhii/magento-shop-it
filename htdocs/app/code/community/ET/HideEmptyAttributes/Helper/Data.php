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
class ET_HideEmptyAttributes_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Getting value for replacement if attribute are empty
     * @return mixed|string
     */
    public function getEmptyAttributeText()
    {
        $defaultValue = '—';
        $value = Mage::getStoreConfig('ethideemptyattributes/general/emptyattrtext');
        if (strlen($value) < 1) {
            $value = $defaultValue;
        }
        return $value;
    }

    /**
     * @return int
     */
    public function ifEnabledHideBooleanNo()
    {
        return (int)Mage::getStoreConfig('ethideemptyattributes/general/booleanattr');
    }

    /**
     * Check if extension is enabled and should hide attributes
     * @return bool
     */
    public function isExtensionEnabled()
    {
        return (bool)Mage::getConfig()->getNode('default/ethideemptyattributes/general/poweroptions');
    }

}