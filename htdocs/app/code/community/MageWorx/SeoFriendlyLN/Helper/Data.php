<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoFriendlyLN_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Disable LN Friendly URLs by conditions.
     * Used in classes:
     * MageWorx_SeoFriendlyLN_Block_Page_Html_Pager
     * MageWorx_SeoFriendlyLN_Block_Catalog_Product_List_Toolbar
     * MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Item
     * MageWorx_SeoFriendlyLN_Model_Catalog_Layer_Filter_Attribute
     *
     * @return boolean
     */
    public function isIndividualLNFriendlyUrlsDisable()
    {
        return false;
    }

    public function _getFilterableAttributes($catId = false)
    {
        if (!is_null(Mage::registry('_layer_filterable_attributes'))) {
            return Mage::registry('_layer_filterable_attributes');
        }
        $attr = array();

        $layerModel = Mage::getModel('catalog/layer');
        if ($catId) {
            $layerModel->setCurrentCategory($catId);
        }
        $attributes = $layerModel->getFilterableAttributes();

        foreach ($attributes as $attribute) {
            $attr[$attribute->getAttributeCode()]['type'] = $attribute->getBackendType();
            $options                                      = $attribute->getSource()->getAllOptions();
            foreach ($options as $option) {
                $attr[$attribute->getAttributeCode()]['options'][$this->formatUrlKey($option['label'])] = $option['label'];
                $attr[$attribute->getAttributeCode()]['frontend_label']                                 = $attribute->getFrontendLabel();
            }
        }
        Mage::register('_layer_filterable_attributes', $attr);
        return $attr;
    }

    public function getLayerFilterUrl($params)
    {
        if (!Mage::helper('seofriendlyln/config')->isLNFriendlyUrlsEnabled()) {
            return Mage::getUrl('*/*/*', $params);
        }

        $hideAttributes = Mage::getStoreConfigFlag('mageworx_seo/seofriendlyln/layered_hide_attributes');
        $urlModel       = Mage::getModel('core/url');
        $queryParams    = $urlModel->getRequest()->getQuery();

        if (isset($queryParams['price']) && is_array($queryParams['price'])) {
            $queryParams['price'] = join(' ', $queryParams['price']);
        }
        if (isset($queryParams['price']) && strpos($queryParams['price'], '-') !== false) {
            $multipliers          = explode('-', $queryParams['price']);
            $priceFrom            = floatval($multipliers[0]);
//            $priceTo              = (!$multipliers[1] ? '' : floatval($multipliers[1]) - 0.01);
            $priceTo              = (!$multipliers[1] ? '' : floatval($multipliers[1]));
            $queryParams['price'] = $priceFrom . '-' . $priceTo;
        }

        foreach ($params['_query'] as $param => $value) {
            $queryParams[$param] = $value;
        }
        $queryParams = array_filter($queryParams);
        //$attr = Mage::registry('_layer_filterable_attributes');
        $attr        = $this->_getFilterableAttributes();

        $layerParams = array();
        foreach ($queryParams as $param => $value) {
            if ($param == 'cat' || isset($attr[$param])) {
                switch ($hideAttributes) {
                    case true:
                        $layerParams[$param == 'cat' ? 0 : $param] = ($param == 'cat' ? $this->formatUrlKey($value) : ($attr[$param]['type']
                                == 'decimal' ? $this->formatUrlKey($param) . Mage::helper('seofriendlyln/config')->getAttributeValueDelimiter() . $value
                                        : $this->formatUrlKey($value)));
                        break;
                    default:
                        $layerParams[$param == 'cat' ? 0 : $param] = ($param == 'cat' ? $this->formatUrlKey($value) : $this->formatUrlKey($param) . Mage::helper('seofriendlyln/config')->getAttributeValueDelimiter() . ($attr[$param]['type']
                                == 'decimal' ? $value : $this->formatUrlKey($value)));
                        break;
                }
                $params['_query'][$param] = null;
            }
        }
        $layer = null;
        if (!empty($layerParams)) {
            uksort($layerParams, 'strcmp');
            $layer = implode('/', $layerParams);
        }
        $url = Mage::getUrl('*/*/*', $params);
        if (!$layer) {
            return $url;
        }

        $urlParts = explode('?', $url, 2);
        $suffix   = Mage::getStoreConfig('catalog/seo/category_url_suffix');

        ///MageWorx fix
        if (strlen($suffix) > 1 and strpos($suffix, '.') === false) {
            $suffix = '.' . $suffix;
        }
        ///MageWorx fix end

        if ($suffix && substr($urlParts[0], -(strlen($suffix))) == $suffix) {
            $url = substr($urlParts[0], 0, -(strlen($suffix)));
        }
        else {
            $url = $urlParts[0];
        }

        $navIdentifier = Mage::helper('seofriendlyln/config')->getLayeredNavigationIdentifier();
        return $url . '/' . $navIdentifier . '/' . $layer . $suffix . (isset($urlParts[1]) ? '?' . $urlParts[1] : '');
    }

    public function formatUrlKey($str)
    {
        return Mage::getSingleton('catalog/category')->formatUrlKey($str);
    }

    public function isCompoundProductType($typeId)
    {
        switch ($typeId) {
            case (Mage_Catalog_Model_Product_Type::TYPE_BUNDLE):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_GROUPED):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE):
                $ret = false;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL):
                $ret = false;
                break;
        }

        return (isset($ret)) ? $ret : null;
    }
}