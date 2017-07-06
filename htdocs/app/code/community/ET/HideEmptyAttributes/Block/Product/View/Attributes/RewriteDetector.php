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
 * @copyright  Copyright (c) 2015 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

/**
 * If OrganicInternet_SimpleConfigurableProducts is installed and used
 * we resolve known rewrite conflict
 */

switch (Mage::registry('et_hideemptyattributes_default')) {
    case 'organic':
        class ET_HideEmptyAttributes_Block_Product_View_Attributes_RewriteDetector
            extends OrganicInternet_SimpleConfigurableProducts_Catalog_Block_Product_View_Attributes
        {
        }
        break;
    default:
        class ET_HideEmptyAttributes_Block_Product_View_Attributes_RewriteDetector
            extends Mage_Catalog_Block_Product_View_Attributes
        {
        }
}

