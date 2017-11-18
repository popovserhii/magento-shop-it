<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

Mage::log('seoreports', null, 'install.log');

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('seosuite_report_product')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_report_product')}` (
  `entity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sku` varchar(64) NOT NULL,
  `url_path` varchar(255) NOT NULL,
  `type_id` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `prepared_name` varchar(255) NOT NULL,
  `name_dupl` smallint(5) unsigned NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `prepared_meta_title` varchar(255) NOT NULL,
  `meta_title_len` tinyint(3) unsigned NOT NULL,
  `meta_title_dupl` smallint(5) unsigned NOT NULL,
  `meta_descr_len` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`),
  KEY `prepared_name` (`prepared_name`(8)),
  KEY `prepared_meta_title` (`prepared_meta_title`(8)),
  KEY `entity_id` (`entity_id`,`product_id`,`store_id`),
  CONSTRAINT `FK_SEOSUITE_PEPORT_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `{$this->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `{$this->getTable('seosuite_report_category')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_report_category')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL,
  `url_path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `prepared_name` varchar(255) NOT NULL,
  `name_dupl` smallint(5) unsigned NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `prepared_meta_title` varchar(255) NOT NULL,
  `meta_title_len` tinyint(3) unsigned NOT NULL,
  `meta_title_dupl` smallint(5) unsigned NOT NULL,
  `meta_descr_len` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`,`store_id`),
  KEY `prepared_name` (`prepared_name`(8)),
  KEY `prepared_meta_title` (`prepared_meta_title`(8))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='';


DROP TABLE IF EXISTS `{$this->getTable('seosuite_report_cms')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('seosuite_report_cms')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` smallint(6) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `url_path` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `prepared_heading` varchar(255) NOT NULL,
  `heading_dupl` smallint(5) unsigned NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `prepared_meta_title` varchar(255) NOT NULL,
  `meta_title_len` tinyint(3) unsigned NOT NULL,
  `meta_title_dupl` smallint(5) unsigned NOT NULL,
  `meta_descr_len` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`,`page_id`,`store_id`),
  KEY `prepared_heading` (`prepared_heading`(8)),
  KEY `prepared_meta_title` (`prepared_meta_title`(8)),
  CONSTRAINT `FK_SEOSUITE_PEPORT_CMS` FOREIGN KEY (`page_id`) REFERENCES `{$this->getTable('cms_page')}` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='';

");

try {
     $pathFrom = 'mageworx_seo/seosuite/product_report_status';
     $pathTo   = 'mageworx_seo/seoreports/product_report_status';
     $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
     if ($collection->count() > 0) {
         foreach ($collection as $coreConfig) {
             $coreConfig->setPath($pathTo)->setValue(0)->save();
         }
     }
 } catch (Exception $e) {
     Mage::log($e->getMessage(), Zend_Log::ERR);
 }

 try {
     $pathFrom = 'mageworx_seo/seosuite/category_report_status';
     $pathTo   = 'mageworx_seo/seoreports/category_report_status';
     $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
     if ($collection->count() > 0) {
         foreach ($collection as $coreConfig) {
             $coreConfig->setPath($pathTo)->setValue(0)->save();
         }
     }
 } catch (Exception $e) {
     Mage::log($e->getMessage(), Zend_Log::ERR);
 }

 try {
     $pathFrom = 'mageworx_seo/seosuite/cms_report_status';
     $pathTo   = 'mageworx_seo/seoreports/cms_report_status';
     $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
     if ($collection->count() > 0) {
         foreach ($collection as $coreConfig) {
             $coreConfig->setPath($pathTo)->setValue(0)->save();
         }
     }
 } catch (Exception $e) {
     Mage::log($e->getMessage(), Zend_Log::ERR);
 }


$installer->endSetup();