<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract extends MageWorx_SeoMarkup_Model_Richsnippet_Abstract
{
    protected function _beforeInit($html)
    {
        $this->_setProduct();

        if (!$this->_product) {
            return false;
        }

        return $this->_cropScriptTags($html);
    }

    protected function _setProduct()
    {
        if (is_callable(array($this->_block, 'getProduct'))) {
            $this->_product = $this->_block->getProduct();
        }

        if (!$this->_product && is_object(Mage::registry('current_product'))) {
            $this->_product = Mage::registry('current_product');
        }
    }
}