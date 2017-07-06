<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Sitemap extends Mage_Core_Model_Abstract
{
    const TEMPORARY_FILE_PATH = "var";

    protected $_filePath;
    protected $_generator;
    protected $_io;

    protected function _construct()
    {
        $this->_init('xsitemap/sitemap');
        $this->_io        = new Varien_Io_File();
        $this->_generator = Mage::getModel('xsitemap/generator');
    }

    protected function _beforeSave()
    {
        $realPath = $this->getFullPath();

        if (!$this->_io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please define correct path'));
        }
        if (!$this->_io->fileExists($realPath, false)) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please create the specified folder "%s" before saving the sitemap.',
                    $this->getSitemapPath()));
        }
        if (!$this->_io->isWriteable($realPath)) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please make sure that "%s" is writable by web-server.',
                    $this->getSitemapPath()));
        }
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.xml$#', $this->getSitemapFilename())) {
            $this->setSitemapFilename($this->getSitemapFilename() . '.xml');
        }
        $this->setSitemapPath($this->_prepareSitemapPath($realPath));
        $this->_checkFileNameIsOriginal();

        return parent::_beforeSave();
    }

    protected function _checkFileNameIsOriginal()
    {
        $otherFilePath = $this->_getOtherModelFullPathFilenames();
        $alreadyIs     = array_search($this->getFullPathFilename(), $otherFilePath);
        if ($alreadyIs) {
            $mes = "The file with such name '%s' exists (sitemap id = %s). Please, select other name for the file.";
            Mage::throwException(Mage::helper('xsitemap')->__($mes, $otherFilePath[$alreadyIs], $alreadyIs));
        }
    }

    protected function _prepareSitemapPath($realPath)
    {
        return rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/';
    }

    protected function _getOtherModelFullPathFilenames()
    {
        $models    = $this->getCollection()->addFieldToFilter('sitemap_id', array('nin' => array($this->getId())))->getItems();
        $filepaths = array();
        foreach ($models as $model) {
            $filepaths[$model->getId()] = $this->getFullPathFilename($model->getSitemapFilename(),
                $model->getSitemapPath());
        }

        return $filepaths;
    }

    protected function _beforeDelete()
    {
        $this->removeFiles();
        return parent::_afterDelete();
    }

    function removeFiles()
    {
        if ($this->getSitemapFilename() && file_exists($this->getFullPathFilename())) {
            $filePathNames = array($this->getFullPathFilename());

            $fileNames = $this->_getFileNamesFromSitemapIndex();

            foreach ($fileNames as $fileName) {
                $filePathNames[] = $this->getFullPathFilename($fileName);
            }

            foreach ($filePathNames as $fullPathName) {
                if (file_exists($fullPathName)) {
                    unlink($fullPathName);
                }
            }
        }
    }

    /*
     * return array from sitemap index
     */
    protected function _getFileNamesFromSitemapIndex($fullPathFilename = false)
    {
        if (!$fullPathFilename) {
            $fullPathFilename = $this->getFullPathFilename();
        }
        $sxml = simplexml_load_file($fullPathFilename);
        if ($sxml) {
            $fileNames = array();
            $i         = 0;
            while (@$sxml->sitemap[$i]->loc instanceof SimpleXMLElement) {
                $el      = $sxml->sitemap[$i]->loc;
                $fileUrl = $el->__toString();
                if ($fileUrl != "" && preg_match('/_([0-9]){3}.xml$/', $fileUrl)) {
                    $urlParts    = explode("/", $fileUrl);
                    $fileName    = array_pop($urlParts);
                    $fileNames[] = $fileName;
                }
                $i++;
            }
            return $fileNames;
        }
    }

    public function getFullPath($io = false, $sitemapPath = false)
    {
        if (!$sitemapPath) {
            $sitemapPath = $this->getSitemapPath();
        }
        return $this->_io->getCleanPath(Mage::getBaseDir() . '/' . $sitemapPath);
    }

    public function getFullTempPath()
    {
        return Mage::getBaseDir(self::TEMPORARY_FILE_PATH) . '/';
    }

    public function getFullPathFilename($sitemapFilename = false, $sitemapPath = false)
    {
        if (!$sitemapFilename) {
            $sitemapFilename = $this->getSitemapFilename();
        }
        return $this->getFullPath(false, $sitemapPath) . $sitemapFilename;
    }

    public function getTotalProduct()
    {
        return $this->_generator->getTotalProduct();
    }

    public function getCounter()
    {
        return $this->_generator->getCounter();
    }

    public function setCounter($num)
    {
        $this->_generator->setCounter($num);
    }

    public function generateXml($entity = false)
    {
        $this->_generator->generateXml($this, $entity);
        $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
        $this->save();
        return $this;
    }

}