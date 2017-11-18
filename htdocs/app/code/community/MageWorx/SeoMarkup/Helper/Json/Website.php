<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Website extends Mage_Core_Helper_Abstract
{
    protected $_product;

    public function getJsonWebSiteData()
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isBreadcrumbsRichsnippetEnabled())
        {
            return false;
        }

        $data = array();
        $data['@context']  = 'http://schema.org';
        $data['@type']     = 'WebSite';
        $data['url']       = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $siteName = Mage::helper('mageworx_seomarkup/config')->getWebsiteName();
        if ($siteName) {
            $data['name'] = $siteName;
        }

        $siteAbout = Mage::helper('mageworx_seomarkup/config')->getWebsiteAboutInfo();
        if ($siteAbout) {
            $data['about'] = $siteAbout;
        }

        $potentialActionData = $this->_getPotentialActionData();
        if ($potentialActionData) {
            $data['potentialAction'] = $potentialActionData;
        }

        return $data;
    }

    protected function _getPotentialActionData()
    {
        if (!Mage::helper('mageworx_seomarkup')->isHomePage()) {
            return false;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isAddWebsiteSearchAction()) {
            return false;
        }

        $storeBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $data = array();
        $data['@type']       = 'SearchAction';
        $data['target']      = $storeBaseUrl . 'catalogsearch/result/?q={search_term_string}';
        $data['query-input'] = 'required name=search_term_string';

        return $data;
    }
}