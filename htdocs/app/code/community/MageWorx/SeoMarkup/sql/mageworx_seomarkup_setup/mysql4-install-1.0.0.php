<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

///Transport setting from SeoSuite Ultimate (before 3.15.0)
$collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', 'mageworx_seo/seosuite/enable_rich_snippets');

if ($collection->count() > 0) {
    try {
        $pathTo   = 'mageworx_seo/richsnippets/enable';
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seobase/product_og_enabled';
        $pathTo   = 'mageworx_seo/richsnippets/product_og_enabled';
        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }
}
$installer->endSetup();