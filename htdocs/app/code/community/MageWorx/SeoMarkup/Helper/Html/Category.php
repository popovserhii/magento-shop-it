<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Html_Category extends Mage_Core_Helper_Abstract
{
    public function getSocialCategoryInfo($head)
    {
        $html  = '';

        if (Mage::helper('mageworx_seomarkup/config')->isCategoryOpenGraphEnabled()) {
            $html .= $this->_getOpenGraphCategoryInfo($head);
        }

        return $html;
    }

    protected function _getOpenGraphCategoryInfo($head)
    {
        $type  = 'product.group';
        $title = $head->getMetaTitle() ? htmlspecialchars($head->getMetaTitle()) : htmlspecialchars($head->getTitle());
        $description = htmlspecialchars($head->getDescription());
        $siteName    = Mage::helper('mageworx_seomarkup/config')->getWebSiteName();

        list($urlRaw) = explode('?', Mage::helper('core/url')->getCurrentUrl());
        $url = rtrim($urlRaw, '/');

        $html  = "\n<meta property=\"og:type\" content=\"" . $type . "\"/>\n";
        $html .= "<meta property=\"og:title\" content=\"" . $title . "\"/>\n";
        $html .= "<meta property=\"og:description\" content=\"" . $description . "\"/>\n";
        $html .= "<meta property=\"og:url\" content=\"" . $url . "\"/>\n";
        if ($siteName) {
            $html .= "<meta property=\"og:site_name\" content=\"" . $siteName . "\"/>\n";
        }

        return $html;
    }
}