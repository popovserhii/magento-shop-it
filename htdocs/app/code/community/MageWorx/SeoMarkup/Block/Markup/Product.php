<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Block_Markup_Product extends Mage_Core_Block_Template
{
    protected function _getMarkupHtml()
    {
        $product = Mage::registry('current_product');

        if (!is_object($product)) {
            return '';
        }

        $html = Mage::helper('mageworx_seomarkup/html_product')->getSocialProductInfo($product);

        $eventJsonData     = Mage::helper('mageworx_seomarkup/json_event')->getJsonEventData($product);
        $eventJson = $eventJsonData ? json_encode($eventJsonData) : '';
        if ($eventJson) {
            $html .= '<script type="application/ld+json">' . $eventJson . '</script>';
        }

        if (!$eventJson) {
            $productJsonData   = Mage::helper('mageworx_seomarkup/json_product')->getJsonProductData($product);
            $productJson = $productJsonData ? json_encode($productJsonData) : '';
            
            if ($productJson) {
                $html .= '<script type="application/ld+json">' . $productJson . '</script>';
            }
        }
        
        return $html;
    }
    
    public function _toHtml()
    {
        return $this->_getMarkupHtml();
    }

}