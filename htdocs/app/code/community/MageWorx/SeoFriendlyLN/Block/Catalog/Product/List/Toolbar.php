<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar extends MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar_Abstract
{

    public function getPagerUrl($params = array())
    {
        if($this->_out()){
            return parent::getPagerUrl($params);
        }

        $urlParams                 = array();
        $urlParams['_current']     = true;
        $urlParams['_escape']      = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query']       = $params;
        return Mage::helper('seofriendlyln')->getLayerFilterUrl($urlParams);
    }

    private function _out()
    {
        if (!Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled()){
            return true;
        }

        if ((string)Mage::getConfig()->getModuleConfig('Amasty_Shopby')->active == 'true'){
            return true;
        }

        if(Mage::helper('seofriendlyln')->isIndividualLNFriendlyUrlsDisable()){
            return true;
        }

        return false;
    }

}
