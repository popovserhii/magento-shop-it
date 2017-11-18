<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Html_Product extends Mage_Core_Helper_Abstract
{
    public function getSocialProductInfo($product)
    {        
        $html = '';

        if (Mage::helper('mageworx_seomarkup/config')->isProductOpenGraphEnabled()) {
            $siteName     = Mage::helper('mageworx_seomarkup/config')->getWebSiteName();
            $url          = Mage::helper('mageworx_seomarkup')->getProductCanonicalUrl($product);
            $descr        = Mage::helper('mageworx_seomarkup')->getDescriptionValue($product);
            $title        = htmlspecialchars($product->getName());
            $color        = Mage::helper('mageworx_seomarkup')->getColorValue($product);
            $categoryName = htmlspecialchars(Mage::helper('mageworx_seomarkup')->getCategoryValue($product));

            $brand        = Mage::helper('mageworx_seomarkup')->getBrandValue($product);
            if (!$brand) {
                $brand = Mage::helper('mageworx_seomarkup')->getManufacturerValue($product);
            }

            $weightString = Mage::helper('mageworx_seomarkup')->getWeightValue($product);
            $weightSep    = strpos($weightString, ' ');
            if ($weightSep !== false) {
                $weightValue  = substr($weightString, 0, $weightSep);
                $weightUnits  = substr($weightString, $weightSep + 1);
            }

            $availability = $this->_getConvertedAvailability($product);
            $condition    = $this->_getConvertedCondition($product);

            $prices = Mage::helper('mageworx_seomarkup/price')->getPricesByProductType($product->getTypeId());

            if (!empty($prices) && is_array($prices)) {
                $price = $prices[0];
            }

            $currency = strtoupper(Mage::app()->getStore()->getCurrentCurrencyCode());
            $html  = "\n";
            $html .= "<meta property=\"og:type\" content=\"product\"/>\n";
            $html .= "<meta property=\"og:title\" content=\"" . $title . "\"/>\n";
            $html .= "<meta property=\"og:description\" content=\"" . $descr . "\"/>\n";
            $html .= "<meta property=\"og:url\" content=\"" . $url . "\"/>\n";

            if (!empty($price)) {
                $html .= "<meta property=\"product:price:amount\" content=\"" . $price . "\"/>\n";

                if ($currency) {
                    $html .= "<meta property=\"product:price:currency\" content=\"" . $currency . "\"/>\n";
                }
            }

            if (Mage::helper('mageworx_seomarkup/config')->isRatingEnabled()) {
                $aggregateRatingData = Mage::helper('mageworx_seomarkup')->getAggregateRatingData($product, false);

                if (Mage::helper('mageworx_seomarkup')->isValidReviewData($aggregateRatingData)) {
                    $html .= "<meta property=\"og:rating_scale\" content=\"" . $aggregateRatingData['bestRating'] . "\"/>\n";
                    $html .= "<meta property=\"og:rating\" content=\"" . $aggregateRatingData['ratingValue'] . "\"/>\n";
                    $html .= "<meta property=\"og:rating_count\" content=\"" . $aggregateRatingData['reviewCount'] . "\"/>\n";
                }
            }

            $html .= "<meta property=\"og:image\" content=\"" . Mage::helper('catalog/image')->init($product, 'image') . "\"/>\n";

            if ($color) {
                $html .= "<meta property=\"og:color\" content=\"" . $color . "\"/>\n";
            }

            if ($brand) {
                $html .= "<meta property=\"og:brand\" content=\"" . $brand . "\"/>\n";
            }

            if ($siteName) {
                $html .= "<meta property=\"og:site_name\" content=\"" . $siteName . "\"/>\n";
            }

            if (!empty($weightValue) && !empty($weightUnits)) {
                $html .= "<meta property=\"product:weight:value\" content=\"" . $weightValue . "\"/>\n";
                $html .= "<meta property=\"product:weight:units\" content=\"" . $weightUnits . "\"/>\n";
            }

            if ($categoryName) {
                $html .= "<meta property=\"og:category\" content=\"" . $categoryName . "\"/>\n";
            }

            if ($availability) {
                $html .= "<meta property=\"og:availability\" content=\"" . $availability . "\"/>\n";
            }

            if ($condition) {
                $html .= "<meta property=\"og:condition\" content=\"" . $condition . "\"/>\n";
            }
        }

        if (Mage::helper('mageworx_seomarkup/config')->isProductTwitterEnabled()) {

            $twitterUsername = Mage::helper('mageworx_seomarkup/config')->getProductTwitterUsername();
            if ($twitterUsername) {
                $html = $html ? $html : "\n";
                $html .= "<meta property=\"twitter:site\" content=\"" . $twitterUsername . "\"/>\n";
                $html .= "<meta property=\"twitter:creator\" content=\"" . $twitterUsername . "\"/>\n";
                $html .= "<meta property=\"twitter:card\" content=\"product\"/>\n";
                $html .= "<meta property=\"twitter:title\" content=\"" . $title . "\"/>\n";
                $html .= "<meta property=\"twitter:description\" content=\"" . $descr . "\"/>\n";
                $html .= "<meta property=\"twitter:url\" content=\"" . $url . "\"/>\n";

                if (!empty($price)) {
                    $html .= "<meta property=\"twitter:label1\" content=\"Price\"/>\n";
                    $html .= "<meta property=\"twitter:data1\" content=\"" . $price . "\"/>\n";
                }

                $html .= "<meta property=\"twitter:label2\" content=\"Availability\"/>\n";
                $html .= "<meta property=\"twitter:data2\" content=\"" . $availability . "\"/>\n";
            }
        }

        return $html;
    }

    protected function _getConvertedCondition($product)
    {
        $condition = Mage::helper('mageworx_seomarkup')->getConditionValue($product);
        if ($condition) {
            $ogEnum = array(
                'NewCondition'         => 'new',
                'UsedCondition'        => 'used',
                'RefurbishedCondition' => 'refurbished',
                'DamagedCondition'     => 'used'
            );
            if (!empty($ogEnum[$condition])) {
                return $ogEnum[$condition];
            }
        }
        return false;
    }

    protected function _getConvertedAvailability($product)
    {
        $availability = Mage::helper('mageworx_seomarkup')->getAvailability($product);
        switch ($availability) {
            case MageWorx_SeoMarkup_Helper_Data::IN_STOCK:
                $availability = 'instock';
                break;
            case MageWorx_SeoMarkup_Helper_Data::OUT_OF_STOCK:
                $availability = 'oos';
                break;
            default:
                $availability = false;
        }
        return $availability;
    }
}