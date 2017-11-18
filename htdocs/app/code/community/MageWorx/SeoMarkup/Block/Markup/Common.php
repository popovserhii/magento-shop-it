<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Block_Markup_Common extends Mage_Core_Block_Template
{
    protected function _getMarkupHtml()
    {
        $html = '';
        
        $websiteJsonData     = Mage::helper('mageworx_seomarkup/json_website')->getJsonWebSiteData();
        $sellerJsonData      = Mage::helper('mageworx_seomarkup/json_seller')->getJsonOrganizationData();
        $breadcrumbsJsonData = $this->_getJsonBreadcrumbsData();

        $websiteJson = $websiteJsonData ? json_encode($websiteJsonData) : '';
        $sellerJson  = $sellerJsonData  ? json_encode($sellerJsonData) : '';

        if ($websiteJsonData) {
            $html .= '<script type="application/ld+json">' . $websiteJson . '</script>';
        }

        if ($sellerJsonData) {
            $html .= '<script type="application/ld+json">' . $sellerJson . '</script>';
        }

        if ($breadcrumbsJsonData) {
            $html .= '<script type="application/ld+json">' . $breadcrumbsJsonData . '</script>';
        }
        return $html;
    }

    protected function _getJsonBreadcrumbsData()
    {
        $breadcrumbs         = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbsJsonData = Mage::helper('mageworx_seomarkup/json_breadcrumbs')->getJsonBreadcrumbsData($breadcrumbs);
        return !empty($breadcrumbsJsonData) ? json_encode($breadcrumbsJsonData) : false;
    }

    public function _toHtml()
    {
        return $this->_getMarkupHtml();
    }

}