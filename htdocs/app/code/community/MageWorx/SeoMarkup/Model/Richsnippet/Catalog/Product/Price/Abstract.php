<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


abstract class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Price_Abstract extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    const FORMAT_CURRENCY = '4';

    const FORMAT_NUMBER_2 = '3';

    const FORMAT_NUMBER_0 = '2';

    const NO_FORMAT = 1;

    protected $_validBlockType = 'Mage_Catalog_Block_Product_View_Abstract';

    protected function _getItemValues($_product = null)
    {
        if (!$_product) {
            $_product = $this->_product;
        }

        $prices = Mage::helper('mageworx_seomarkup/price')->getDefaultPrices($_product);

        $modPrices = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $modPrices = array_merge($modPrices, $this->_getModifyPrices($price));
            }
        }

        return array_unique($modPrices);
    }

    protected function _nodeNotFound()
    {
        $report = new Varien_Object(array('classname' => get_class($this)));
        Mage::register('mageworx_richsnippet_price_report', $report, true);
        parent::_nodeNotFound();
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        $firstParentNode = $this->_findParentContainer($node);
        if ($firstParentNode) {
            $secondParentNode = $this->_findParentContainer($firstParentNode);
            if ($secondParentNode) {
                return true;
            }
        }
        return false;
    }

    protected function _getModifyPrices($price, $deep = 4)
    {
        $key = (string)$price;

        $prices = array();
        switch ($deep) {
            case 4:
                $prices[$this->_packNodeKey($key, self::FORMAT_CURRENCY)] = Mage::helper('core')->currency($price, true, false);
            case 3:
                $prices[$this->_packNodeKey($key, self::FORMAT_NUMBER_2)] = number_format($price, 2);
            case 2:
                $prices[$this->_packNodeKey($key, self::FORMAT_NUMBER_0)] = number_format($price, 0);
            case 1:
                $prices[$this->_packNodeKey($key, self::NO_FORMAT)]       = $price;
                break;
        }

        return array_unique($prices);
    }

    protected function _packNodeKey($key, $identifier)
    {
        return $key . $this->_getNodeKeyGlue() . $identifier;
    }

    protected function _unpackNodeKey($nodeKey)
    {
        return explode($this->_getNodeKeyGlue(), $nodeKey);
    }

    protected function _getNodeKeyGlue()
    {
        return ':::';
    }

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $priceNode = $this->_findParentContainer($node);
        $offerNode = $this->_findParentContainer($priceNode);

        if ($offerNode && $priceNode) {
            $priceNode->itemprop  = 'price';
            $offerNode->itemtype  = MageWorx_SeoMarkup_Helper_Data::OFFER;
            $offerNode->itemscope = '';
            $offerNode->itemprop  = 'offers';
        }

        if (Mage::helper('mageworx_seomarkup/config')->isAddMetaPrice() && $this->_nodeKey) {
            $price         = $this->_formatPrice();

            if ($price) {
                $priceNode->removeAttribute("itemprop");
                $offerNode->innertext = $offerNode->innertext .
                    "<meta itemprop='price' content='{$price}' />\n ";
            }
        }

        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        if ($currency_code) {
            $offerNode->innertext = $offerNode->innertext .
                "\n<meta itemprop='priceCurrency' content='{$currency_code}' />\n";
        }

        return true;
    }

    protected function _formatPrice()
    {
        //$typeFormat may be used for inverse price format
        list($price, $typePriceFormat) = $this->_unpackNodeKey($this->_nodeKey);

        return $price;
    }
    protected function _checkBlockType()
    {

    }

    protected function _afterRender()
    {
        $report = new Varien_Object(array('status' => 'success'));
        Mage::register('mageworx_richsnippet_price_report', $report, true);
        return parent::_afterRender();
    }

}
