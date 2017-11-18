<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



if ((string)Mage::getConfig()->getModuleConfig('Amasty_Sorting')->active == 'true'){
    class MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_Abstract extends Amasty_Sorting_Block_Catalog_Product_List_Toolbar {}
}
elseif ((string)Mage::getConfig()->getModuleConfig('Amasty_Shopby')->active == 'true'){
    class MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_Abstract extends Amasty_Shopby_Block_Catalog_Product_List_Toolbar {}
}
elseif ((string)Mage::getConfig()->getModuleConfig('Itactica_LayeredNavigation')->active == 'true'){
    class MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_Abstract extends Itactica_LayeredNavigation_Block_Catalog_Product_List_Toolbar {}
}
else {
    class MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_Abstract extends Mage_Catalog_Block_Product_List_Toolbar {}
}