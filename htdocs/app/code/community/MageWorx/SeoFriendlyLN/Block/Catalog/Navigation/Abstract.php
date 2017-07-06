<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


if ((string) Mage::getConfig()->getModuleConfig('Brandammo_Pronav')->active == 'true') {

    class MageWorx_SeoFriendlyLN_Block_Catalog_Navigation_Abstract extends Brandammo_Pronav_Block_NavigationTop
    {

    }

}
else if ((string) Mage::getConfig()->getModuleConfig('JR_CleverCms')->active == 'true') {

    class MageWorx_SeoFriendlyLN_Block_Catalog_Navigation_Abstract extends JR_CleverCms_Block_Catalog_Navigation
    {

    }

}
else {

    class MageWorx_SeoFriendlyLN_Block_Catalog_Navigation_Abstract extends Mage_Catalog_Block_Navigation
    {

    }

}