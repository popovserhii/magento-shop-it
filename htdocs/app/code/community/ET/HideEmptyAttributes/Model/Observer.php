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
 * @copyright  Copyright (c) 2014 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */
class ET_HideEmptyAttributes_Model_Observer extends Varien_Object
{

    /**
     * Rewrite necessary blocks
     *
     * @param Varien_Event_Observer $observer
     */
    public function rewriteClasses(Varien_Event_Observer $observer)
    {
        /** @var $helper ET_HideEmptyAttributes_Helper_Data*/
        $helper = Mage::helper('ethideemptyattributes');

        $isRewriteEnabled = $helper->isExtensionEnabled();
        if ($isRewriteEnabled) {

            $this->resolveRewriteConflict();

            //Block rewrite
            Mage::getConfig()->setNode('global/blocks/catalog/rewrite/product_view_attributes',
                'ET_HideEmptyAttributes_Block_Default');

            Mage::getConfig()->setNode('global/blocks/catalog/rewrite/product_compare_list',
                'ET_HideEmptyAttributes_Block_Compare');

        }
    }


    /**
     * Check for known conflicts.
     * Register information about possible resolving.
     * This information is used in RewriteDetector block.
     *
     */
    public function resolveRewriteConflict()
    {
        $currentBlockClass = (string)Mage::getConfig()->getNode('global/blocks/catalog/rewrite/product_view_attributes');

        switch ($currentBlockClass){
            case 'OrganicInternet_SimpleConfigurableProducts_Catalog_Block_Product_View_Attributes':
                Mage::register('et_hideemptyattributes_default', 'organic');
                break;
            default:
                Mage::register('et_hideemptyattributes_default', 'default');

        }
    }

}