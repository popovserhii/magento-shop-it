<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Html_Page extends Mage_Core_Helper_Abstract
{
    public function getSocialPageInfo($head)
    {
        $html  = '';

        if (Mage::helper('mageworx_seomarkup')->isHomePage() &&
           Mage::helper('mageworx_seomarkup/config')->isWebsiteOpenGraphEnabled()
        ) {
            $html .= $this->_getOpenGraphPageInfo($head, true);

        } elseif (Mage::helper('mageworx_seomarkup/config')->isPageOpenGraphEnabled()) {
            $html .= $this->_getOpenGraphPageInfo($head);
        }

        if (Mage::helper('mageworx_seomarkup')->isHomePage() &&
            Mage::helper('mageworx_seomarkup/config')->isWebsiteTwitterEnabled() &&
            Mage::helper('mageworx_seomarkup/config')->getWebsiteTwitterUsername()
        ) {
            $html .= $this->_getTwitterPageInfo($head, true);

        } elseif (Mage::helper('mageworx_seomarkup/config')->isPageTwitterEnabled() &&
                  Mage::helper('mageworx_seomarkup/config')->getPageTwitterUsername()
        ) {
            $html .= $this->_getTwitterPageInfo($head);
        }

        return $html;
    }

    protected function _getOpenGraphPageInfo($head, $isWebsite = false)
    {
        if ($isWebsite) {
            $type     = 'website';
            $imageUrl = $this->_getFacebookLogoUrl();
        } else {
            $type     = 'article';
            $imageUrl = '';
        }

        $title = $head->getMetaTitle() ? htmlspecialchars($head->getMetaTitle()) : htmlspecialchars($head->getTitle());
        $description = htmlspecialchars($head->getDescription());
        $siteName = Mage::helper('mageworx_seomarkup/config')->getWebSiteName();

        list($urlRaw) = explode('?', Mage::helper('core/url')->getCurrentUrl());
        $url = rtrim($urlRaw, '/');

        $html = "\n<meta property=\"og:type\" content=\"" . $type . "\"/>\n";
        $html .= "<meta property=\"og:title\" content=\"" . $title . "\"/>\n";
        $html .= "<meta property=\"og:description\" content=\"" . $description . "\"/>\n";
        $html .= "<meta property=\"og:url\" content=\"" . $url . "\"/>\n";
        if ($siteName) {
            $html .= "<meta property=\"og:site_name\" content=\"" . $siteName . "\"/>\n";
        }
        if($imageUrl) {
            $html .= "<meta property=\"og:image\" content=\"" . $imageUrl . "\"/>\n";
        }

        return $html;
    }

    protected function _getTwitterPageInfo($head, $isWebsite = false)
    {
        if ($isWebsite) {
            $type            = 'summary_large_image';
            $twitterUsername = Mage::helper('mageworx_seomarkup/config')->getWebsiteTwitterUsername();
            $imageUrl        = $this->_getTwitterLogoUrl();
        } else {
            $type            = 'summary';
            $twitterUsername = Mage::helper('mageworx_seomarkup/config')->getPageTwitterUsername();
            $imageUrl        = '';
        }

        $title = $head->getMetaTitle() ? htmlspecialchars($head->getMetaTitle()) : htmlspecialchars($head->getTitle());
        $description = htmlspecialchars($head->getDescription());
        
        $html = "<meta property=\"twitter:card\" content=\"" . $type . "\"/>\n";
        $html .= "<meta property=\"twitter:site\" content=\"" . $twitterUsername . "\"/>\n";
        $html .= "<meta property=\"twitter:title\" content=\"" . $title . "\"/>\n";
        $html .= "<meta property=\"twitter:description\" content=\"" . $description . "\"/>\n";

        if($imageUrl) {
            $html .= "<meta property=\"twitter:image\" content=\"" . $imageUrl . "\"/>\n";
        }
        
        return $html;
    }

    /**
     * Retrieve path to Facebook Website Logo
     *
     * @return string
     */
    protected function _getFacebookLogoUrl()
    {
        $folderName = MageWorx_SeoMarkup_Model_System_Config_Backend_LogoFacebook::UPLOAD_DIR;
        $storeConfig = Mage::helper('mageworx_seomarkup/config')->getFacebookLogoFile();
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        
        if(!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            return $faviconFile;
        } 
        return false;
    }

    /**
     * Retrieve path to Facebook Website Logo
     *
     * @return string
     */
    protected function _getTwitterLogoUrl()
    {
        $folderName = MageWorx_SeoMarkup_Model_System_Config_Backend_LogoTwitter::UPLOAD_DIR;
        $storeConfig = Mage::helper('mageworx_seomarkup/config')->getTwitterLogoFile();
        $faviconFile = Mage::getBaseUrl('media') . $folderName . '/' . $storeConfig;
        $absolutePath = Mage::getBaseDir('media') . '/' . $folderName . '/' . $storeConfig;

        if(!is_null($storeConfig) && $this->_isFile($absolutePath)) {
            return $faviconFile;
        }
        return false;
    }

    protected function _isFile($filename) {
        if (Mage::helper('core/file_storage_database')->checkDbUsage() && !is_file($filename)) {
            Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }
        return is_file($filename);
    }
}