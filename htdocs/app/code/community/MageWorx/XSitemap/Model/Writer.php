<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Writer
{
    protected $_init          = false;
    protected $_io;
    protected $_filePath;
    protected $_fileName;
    protected $_tempFilePath;
    protected $_xmlHeaderWrite;
    protected $_xmlFooterWrite;
    protected $_hasImagesLink    = true;
    protected $_hasAlternateLink = true;
    protected $_useCssForXmlSitemap = false;
    protected $_useIndex      = 5;
    protected $_maxLinks      = 50000;
    protected $_splitSize     = 10000000;
    protected $_sitemapInc    = 1;
    protected $_currentInc    = 0;
    protected $_action;
    protected $_helper;

    /**
     * storeBaseUrl need only for generate sitemapindex
     *
     * @var type string
     */
    protected $_storeBaseUrl;

    const ALLOW_CREATE_FOLDERS       = true;
    const SYSTEM_TEMPORARY_FILE_NAME = 'mageworx_xsitemap.temp';

    public function init($filePath, $fileName, $tempFilePath, $header = true, $footer = true, $storeBaseUrl = false)
    {
        $this->_helper = Mage::helper('xsitemap');

        $this->_filePath       = $filePath;
        $this->_fileName       = $fileName;
        $this->_tempFilePath   = $tempFilePath;
        $this->_xmlHeaderWrite = $header;
        $this->_xmlFooterWrite = $footer;

        $this->_loadParamsFromConfig();

        $this->_cssPath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/css/mageworx/xsitemap/xsitemap_xml.css';

        $this->_changefreqValidator = new Zend_Validate_Sitemap_Changefreq();
        $this->_lastmodValidator    = new Zend_Validate_Sitemap_Lastmod();
        $this->_locationValidator   = new Zend_Validate_Sitemap_Loc();
        $this->_priorityValidator   = new Zend_Validate_Sitemap_Priority();

        if ($this->_useIndex && !$storeBaseUrl) {
            throw new Exception($this->_helper->__('The sitemap index file can\'t be created without storeBaseUrl . Process is canceled.'));
        }
        else {
            $this->_storeBaseUrl = $storeBaseUrl;
        }

        $this->_io = new Varien_Io_File();
        $this->_io->setAllowCreateFolders(self::ALLOW_CREATE_FOLDERS);

        $this->_action = $this->_getAction();

        if ($this->_action == 'continue_write' || $this->_action == 'end_write') {
            $this->_loadParamsFromSystemTemporaryFile();
        }
        $this->_openXml();
        $this->_init = true;
    }

    protected function _loadParamsFromConfig()
    {
        $hasImagesLink = Mage::helper('xsitemap')->isProductImages();
        if (isset($hasImagesLink) and $hasImagesLink !== '') {
            $this->_hasImagesLink = $hasImagesLink;
        }

        $hasAlternateLink = Mage::helper('xsitemap')->isAlternateUrlsEnabled();
        if (isset($hasAlternateLink) and $hasAlternateLink !== '') {
            $this->_hasAlternateLink = $hasAlternateLink;
        }

        $this->_useCssForXmlSitemap = Mage::helper('xsitemap')->isUseCssForXmlSitemap();

        $useIndex = Mage::helper('xsitemap')->useIndex();
        if (isset($useIndex) and $useIndex !== '') {
            $this->_useIndex = $useIndex;
        }

        $splitSize = Mage::helper('xsitemap')->getSplitSize();
        if (isset($splitSize) and $splitSize !== '') {
            $this->_splitSize = ($splitSize < 20480) ? $this->_splitSize = 20480 : $splitSize;
        }

        $maxLinks = Mage::helper('xsitemap')->getMaxLinks();
        if (isset($maxLinks) and $maxLinks !== '') {
            $this->_maxLinks = $maxLinks;
        }
    }

    protected function _getAction()
    {
        if ($this->_xmlHeaderWrite && $this->_xmlFooterWrite) {
            return 'completely_write';
        }
        elseif ($this->_xmlHeaderWrite && !$this->_xmlFooterWrite) {
            return 'begin_write';
        }
        elseif (!$this->_xmlHeaderWrite && !$this->_xmlFooterWrite) {
            return 'continue_write';
        }
        else {
            return 'end_write';
        }
    }

    protected function _loadParamsFromSystemTemporaryFile()
    {
        $this->_openPathAndFileExistForSystemTemporaryFile();
        $string = $this->_io->read($this->_tempFilePath . self::SYSTEM_TEMPORARY_FILE_NAME);
        $params = unserialize($string);
        if (is_array($params)) {
            if (!empty($params['sitemapInc'])) {
                $this->_sitemapInc = $params['sitemapInc'];
                $this->_currentInc = $params['currentInc'];
            }
            else {
                throw new Exception($this->_helper->__("Temporary system file is corrupt. Params for sitemap index not be loaded."));
            }
        }
    }

    //@todo: find solve for fast check xml file (size about 10 Mb);
    protected function _isXmlFileValid($fileName = false)
    {
        return true;
        //uncomment here for test mode
//        if (!$fileName) {
//            $fileName = $this->_getSitemapFilename();
//        }
//        $result = @simplexml_load_file($this->_tempFilePath . $fileName);
//        return (!$result) ? false : true;
    }

    protected function _deleteSystemTemporaryFile()
    {
        if ($this->_io->fileExists($this->_tempFilePath . self::SYSTEM_TEMPORARY_FILE_NAME)) {
            $this->_io->rm(self::SYSTEM_TEMPORARY_FILE_NAME);
        }
    }

    protected function _writeParamsInSystemTemporaryFile()
    {
        $this->_openPathAndFileExistForSystemTemporaryFile();
        $this->_io->streamOpen(self::SYSTEM_TEMPORARY_FILE_NAME, 'w+');
        $string = serialize(array('sitemapInc' => $this->_sitemapInc, 'currentInc' => $this->_currentInc));
        $this->_io->streamWrite($string);
        $this->_io->close();
    }

    public function write($rawUrl, $lastmod, $changefreq, $priority, $imageUrls = false, $alternateUrls = false)
    {
        if (!$this->_init) {
            Mage::throwException($this->_helper->__('Sitemap Writer class wasn\'t initialized.'));
        }

        $url = htmlspecialchars($rawUrl);
        $this->_isInputDataValid($url, $lastmod, $changefreq, $priority, $imageUrls);

        $imagePartXml         = "";
        $alternateUrlsPartXml = "";

        $countAdditionalLinks = 0;

        if ($this->_hasImagesLink) {
            if (is_array($imageUrls) && count($imageUrls) > 0) {
                $countAdditionalLinks += count($imageUrls);
                foreach ($imageUrls as $imageUrl) {
                    $imagePartXml .= '<image:image><image:loc>' . htmlspecialchars($imageUrl) . '</image:loc></image:image>';
                }
            }
        }

        if($this->_hasAlternateLink){
            if(is_array($alternateUrls) && count($alternateUrls) > 0) {
                $countAdditionalLinks += count($alternateUrls);
                foreach ($alternateUrls as $hreflang => $altUrl) {
                    $alternateUrlsPartXml .= '<xhtml:link rel="alternate" hreflang="' . $hreflang . '" href="' . $altUrl . '"/>';
                }
            }
        }

        $this->_checkSitemapLimits($countAdditionalLinks);

        $xml = sprintf('<url><loc>%s</loc>%s<lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority>%s</url>',
            $url, $alternateUrlsPartXml, $lastmod, $changefreq, $priority, $imagePartXml);

        $this->_io->streamWrite($xml);
    }

    protected function _isInputDataValid($url, $lastmod, $changefreq, $priority)
    {
        if ($this->_locationValidator->isValid($url) == false && $this->_helper->isValidateUrls()) {
            throw new Exception($this->_helper->__("Location value '%s' is not valid.", $url));
        }
        if ($this->_changefreqValidator->isValid($changefreq) == false) {
            throw new Exception($this->_helper->__("Changefreq value '%s' is not valid. Item url: '%s'.", $changefreq,
                $url));
        }
        if ($this->_lastmodValidator->isValid($lastmod) == false) {
            throw new Exception($this->_helper->__("Lastmod value '%s' is not valid. Item url: '%s'.", $lastmod, $url));
        }
        if ($this->_priorityValidator->isValid($priority) == false) {
            throw new Exception($this->_helper->__("Priority value '%s' is not valid. Item url: '%s'.", $priority,
                $url));
        }
    }

    protected function _openPathAndFileExist($type)
    {
        switch ($type) {
            case "temporary":
                $filePath = $this->_tempFilePath;
                $fileName = $this->_getSitemapFilename();
                break;
            case "system_temporary":
                $filePath = $this->_tempFilePath;
                $fileName = self::SYSTEM_TEMPORARY_FILE_NAME;
                break;
            case "original":
                $filePath = $this->_filePath;
                $fileName = $this->_getSitemapFilename();
                break;
            default:
                throw new Exception($this->_helper->__("Wrong param in sitemap openPath function."));
        }

        $this->_io->open(array('path' => $filePath));
        if ($this->_io->fileExists($fileName) && !$this->_io->isWriteable($fileName)) {
            Mage::throwException($this->_helper->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.',
                    $fileName, $filePath));
        }
    }

    protected function _openPathAndFileExistForOriginalFile()
    {
        $this->_openPathAndFileExist("original");
    }

    protected function _openPathAndFileExistForTemporaryFile()
    {
        $this->_openPathAndFileExist("temporary");
    }

    protected function _openPathAndFileExistForSystemTemporaryFile()
    {
        $this->_openPathAndFileExist("system_temporary");
    }

    protected function _openXml($headerWrite = false)
    {
        $this->_openPathAndFileExistForOriginalFile();
        $this->_openPathAndFileExistForTemporaryFile();
        $mode = ($this->_xmlHeaderWrite || $headerWrite) ? 'w+' : 'a+';
        $this->_io->streamOpen($this->_getSitemapFilename(), $mode);

        if ($mode == 'w+') {
            $this->_writeXmlHeader();
        }
    }

    protected function _writeXmlHeader()
    {
        $this->_io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");

        $add = "";
        $css = "";

        if ($this->_hasImagesLink) {
            $add .= "\n" . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        }

        if ($this->_hasAlternateLink) {
            $add .= "\n" . ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';
        }

        if($this->_useCssForXmlSitemap){
            $css = '<?xml-stylesheet type="text/css" href="' . $this->_cssPath . '"?>';
        }

        $this->_io->streamWrite($css . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $add .'>');
    }

    protected function _checkSitemapLimits($countAdditionalLinks = 0)
    {
        if ($this->_useIndex) {
            if ($this->_currentInc + $countAdditionalLinks >= $this->_maxLinks) {
                $this->_currentInc = 0;
                $this->_closeXml(true);
                $this->_sitemapInc++;
                $this->_openXml(true);
            }
            $this->_currentInc += 1 + $countAdditionalLinks;
        }
    }

    protected function _getSitemapFilename()
    {
        if ($this->_useIndex) {
            $sitemapFilename = $this->_fileName;
            $ext             = strrchr($sitemapFilename, '.');
            $sitemapFilename = substr($sitemapFilename, 0, strlen($sitemapFilename) - strlen($ext)) . '_' . sprintf('%03s',
                    $this->_sitemapInc) . $ext;
            return $sitemapFilename;
        }
        return trim($this->_fileName, '/');
    }

    /**
     * param _action = end_write || completely_write use when method call from __destruct
     * param $close = true use when method call from _checkSitemapLimits (when use sitemap)
     * @param type $close bool
     */
    public function _closeXml($close = false)
    {
        if ($this->_action == "end_write" || $this->_action == "completely_write" || $close == true) {
            //reopen because io close stream when __destruct
            $this->_io->streamOpen($this->_getSitemapFilename(), "a+");
            $this->_io->streamWrite('</urlset>');
            $this->_io->streamClose();

            if (!$this->_isXmlFileValid()) {
                throw new Exception($this->_helper->__("Sitemap xml file isn't valid."));
            }

            if (!$this->_useIndex) {
                $this->_moveFileFromTempToOriginal();
            }
        }
    }

    protected function _moveFileFromTempToOriginal($fileName = false)
    {
        if (!$fileName) {
            $fileName = $this->_getSitemapFilename();
        }
        $from   = $this->_tempFilePath . $fileName;
        $to     = $this->_filePath . $fileName;
        $result = $this->_io->mv($from, $to);
        if (!$result) {
            throw new Exception($this->_helper->__("Relocation of the file \'%s\' to \'%s\' is impossible.", $from, $to));
        }
    }

    protected function _moveSitemapIndexFiles()
    {
        $i = $this->_sitemapInc;
        for ($this->_sitemapInc = 1; $this->_sitemapInc < $i; $this->_sitemapInc++) {
            $this->_moveFileFromTempToOriginal();
        }
        $this->_moveFileFromTempToOriginal($this->_fileName);
    }

    protected function _generateSitemapIndex()
    {
        if (!$this->_useIndex) {
            return;
        }

        $this->_openPathAndFileExistForOriginalFile();
        $this->_openPathAndFileExistForTemporaryFile();

        $this->_io->streamOpen($this->_fileName);
        $this->_io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $this->_io->streamWrite('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $i    = $this->_sitemapInc;

        for ($this->_sitemapInc = 1; $this->_sitemapInc <= $i; $this->_sitemapInc++) {
            //$fileName = preg_replace('/^\//', '', "/" . $this->_getSitemapFilename());
            $fileName = $this->_getSitemapFilename();
            if (file_exists($this->_tempFilePath . $fileName)) {
                $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                    htmlspecialchars($this->_storeBaseUrl . $fileName), $date
                );
                $this->_io->streamWrite($xml);
            }
        }

        $sitemapLinks = $this->_getSitemapLinks();
        if (is_array($sitemapLinks)) {
            foreach ($sitemapLinks as $sitemapLink) {
                $date        = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
                $sitemapLink = trim($sitemapLink);
                if ($sitemapLink) {
                    $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                        htmlspecialchars($sitemapLink), $date);
                    $this->_io->streamWrite($xml);
                }
            }
        }

        $this->_io->streamWrite('</sitemapindex>');
        $this->_io->streamClose();

        if (!$this->_isXmlFileValid($this->_fileName)) {
            throw new Exception($this->_helper->__("Sitemap index xml file '%s' isn\'t valid.",
                $this->_getSitemapFilename()));
        }

        $this->_moveSitemapIndexFiles();
    }

    protected function _getSitemapLinks()
    {
        return $this->_helper->getSitemapFileLinks();
    }

    public function __destruct()
    {
        $this->_closeXml();

        if ($this->_action == 'begin_write' || $this->_action == 'continue_write') {
            $this->_writeParamsInSystemTemporaryFile();
        }

        if ($this->_action == 'end_write' || $this->_action == 'completely_write') {
            $this->_generateSitemapIndex();
        }

        if ($this->_action == 'end_write') {
            $this->_deleteSystemTemporaryFile();
        }
    }
}