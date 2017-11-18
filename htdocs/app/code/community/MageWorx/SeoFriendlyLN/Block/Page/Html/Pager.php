<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Block_Page_Html_Pager extends MageWorx_SeoFriendlyLN_Block_Page_Html_Pager_Abstract
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

        $url = Mage::helper('seofriendlyln')->getLayerFilterUrl($urlParams);
        $pagerUrlFormat = Mage::helper('seofriendlyln/config')->getPagerUrlFormat();

        if (isset($params['p']) && Mage::app()->getRequest()->getControllerName() == 'category' && $pagerUrlFormat) {
            $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');

            if (strlen($suffix) > 1 and strpos($suffix, '.') === false) {
                $suffix = '.' . $suffix;
            }

            $pageNum = $params['p'];
            $url     = str_replace(array('&amp;p=' . $pageNum, '&p=' . $pageNum, '?p=' . $pageNum), '', $url);
            if ($pageNum > 1) {

                if(strpos($url, '?') !== false){
                    $urlArr = explode('?', $url);
                }else{
                    $urlArr = explode('&amp;', $url);
                }

                $urlArr[0] = ($suffix && substr($urlArr[0], -(strlen($suffix))) == $suffix ? substr($urlArr[0], 0,
                            -(strlen($suffix))) : $urlArr[0]);
                $urlArr[0] .= str_replace('[page_number]', $pageNum, $pagerUrlFormat);
                $urlArr[0] .= $suffix;
                $url       = implode('?', $urlArr);
            }
        }
        return $url;
    }

    private function _out()
    {
        if (!Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled()){
            return true;
        }

        if(Mage::helper('seofriendlyln')->isIndividualLNFriendlyUrlsDisable()){
            return true;
        }

        if(Mage::app()->getRequest()->getControllerName() != 'category'){
            return true;
        }

        return false;
    }
}