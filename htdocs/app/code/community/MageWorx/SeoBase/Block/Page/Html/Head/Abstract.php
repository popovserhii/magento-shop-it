<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

/**
 * This class was deprecated since SeoBase 3.18.0.
 * Now it is used only for compatibility with previous fixes of class rewrites.
 */

if ((string)Mage::getConfig()->getModuleConfig('MageWorx_Accelerator')->active == 'true') {
    class MageWorx_SeoBase_Block_Page_Html_Head_Abstract extends MageWorx_Accelerator_Block_Page_Html_Head {}
} elseif ((string)Mage::getConfig()->getModuleConfig('Fooman_Speedster')->active == 'true') {
    class MageWorx_SeoBase_Block_Page_Html_Head_Abstract extends Fooman_Speedster_Block_Page_Html_Head {}
} elseif ((string)Mage::getConfig()->getModuleConfig('Mage_External')->active == 'true') {
    class MageWorx_SeoBase_Block_Page_Html_Head_Abstract extends Mage_External_Block_Html_Head {}
} elseif ((string)Mage::getConfig()->getModuleConfig('Inchoo_Xternal')->active == 'true') {
    class MageWorx_SeoBase_Block_Page_Html_Head_Abstract extends Inchoo_Xternal_Block_Html_Head {}    
} elseif ((string)Mage::getConfig()->getModuleConfig('GT_Speed')->active == 'true') {
    class MageWorx_SeoBase_Block_Page_Html_Head_Abstract extends GT_Speed_Block_Page_Html_Head {}
} else {
    class MageWorx_SeoBase_Block_Page_Html_Head_Abstract extends Mage_Page_Block_Html_Head {}
}