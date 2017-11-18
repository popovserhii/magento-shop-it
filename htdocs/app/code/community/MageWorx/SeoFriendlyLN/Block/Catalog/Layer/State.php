<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoFriendlyLN_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State
{

    public function getClearUrl()
    {
        if(!Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled()){
            return parent::getClearUrl();
        }
        $filterState = array();
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $filterState;
        $params['_escape']      = true;
        return Mage::helper('seofriendlyln')->getLayerFilterUrl($params);
    }

}
