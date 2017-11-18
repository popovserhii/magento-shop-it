<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();
/**
 * @todo    Check default magento value
 */
Mage::log('seofriendlyln', null, 'install.log');

///Transport setting from SeoSuite Ultimate (before 3.15.0)
$collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', 'mageworx_seo/seosuite/layered_hide_attributes');

if ($collection->count() > 0) {
    Mage::log('seofriendlyln-transport', null, 'install.log');
    try {
        $pathTo   = 'mageworx_seo/seofriendlyln/layered_hide_attributes';
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathOld1 = 'mageworx_seo/seosuite/disable_layered_rewrites';
        $pathOld2 = 'mageworx_seo/seosuite/layered_friendly_urls';
        $pathTo   = 'mageworx_seo/seosuite/enable_ln_friendly_urls';

        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathOld1);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                if((bool)$coreConfig->getValue()){
                    $coreConfig->setValue(0);
                }else{
                    $coreConfig->setValue(1);
                }
                $coreConfig->setPath($pathTo)->save();
            }
        }

        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathOld2);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->delete();
            }
        }

    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seosuite/enable_ln_friendly_urls';
        $pathTo   = 'mageworx_seo/seofriendlyln/enable_ln_friendly_urls';
        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seosuite/layered_identifier';
        $pathTo   = 'mageworx_seo/seofriendlyln/layered_identifier';
        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seosuite/layered_separatort';
        $pathTo   = 'mageworx_seo/seofriendlyln/layered_separatort';
        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seosuite/pager_url_format';
        $pathTo   = 'mageworx_seo/seofriendlyln/pager_url_format';
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