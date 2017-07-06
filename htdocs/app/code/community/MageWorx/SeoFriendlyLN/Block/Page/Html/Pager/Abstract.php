<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

if((string) Mage::getConfig()->getModuleConfig('EcommerceTeam_Sln')->active == 'true') {

    class MageWorx_SeoFriendlyLN_Block_Page_Html_Pager_Abstract extends EcommerceTeam_Sln_Block_Page_Pager
    {

    }
}
else {

    class MageWorx_SeoFriendlyLN_Block_Page_Html_Pager_Abstract extends Mage_Page_Block_Html_Pager
    {

    }

}