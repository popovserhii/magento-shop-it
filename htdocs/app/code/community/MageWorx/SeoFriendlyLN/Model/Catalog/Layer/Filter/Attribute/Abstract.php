<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



if ((string)Mage::getConfig()->getModuleConfig('Amasty_Shopby')->active == 'true'){
    class MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Attribute_Abstract extends Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute {}
} elseif ((string)Mage::getConfig()->getModuleConfig('Itactica_LayeredNavigation')->active == 'true') {
    class MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Attribute_Abstract extends Itactica_LayeredNavigation_Model_Catalog_Layer_Filter_Attribute {}
} else {
    class MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Attribute_Abstract extends Mage_Catalog_Model_Layer_Filter_Attribute {}
}