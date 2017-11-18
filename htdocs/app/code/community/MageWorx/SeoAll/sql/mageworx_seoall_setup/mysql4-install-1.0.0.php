<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;

$seoAllTrailingSlashCollection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter(
    'path', array('like' => 'mageworx_seo/seoall/trailing_slash_home_page')
);

if ($seoAllTrailingSlashCollection->count() == 0) {
    $seoTrailingSlashCollection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter(
        'path', array('like' => 'mageworx_seo/seobase/trailing_slash_home_page')
    );

    if ($seoTrailingSlashCollection->count() == 0) {
        $seoTrailingSlashCollection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter(
            'path', array('like' => 'mageworx_seo/xsitemap_common/trailing_slash_home_page')
        );
    }

    if ($seoTrailingSlashCollection->count() > 0) {
        foreach ($seoTrailingSlashCollection as $coreConfig) {
            $coreConfig->setConfigId(null);

            $from = array('mageworx_seo/seobase', 'mageworx_seo/xsitemap_common');

            $path = str_replace($from, 'mageworx_seo/seoall', $coreConfig->getPath());
            $coreConfig->setPath($path)->save();
        }
    }
}

$installer->endSetup();