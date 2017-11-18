<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


if ((string) Mage::getConfig()->getModuleConfig('MageWorx_SearchAutocomplete')->active == 'true') {

    class MageWorx_SeoBase_Block_Review_Helper_Abstract extends MageWorx_SearchAutocomplete_Block_Review_Helper
    {

    }

}
else {

    class MageWorx_SeoBase_Block_Review_Helper_Abstract extends Mage_Review_Block_Helper
    {

    }

}