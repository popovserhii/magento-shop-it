<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

///Transport setting from SeoSuite Ultimate (before 3.15.0)
$collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', 'mageworx_seo/seosuite/optimized_urls');

if ($collection->count() > 0) {
    try {
        $pathTo   = 'mageworx_seo/seoextended/optimized_urls';
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seosuite/status_pager_num_for_title';
        $pathTo   = 'mageworx_seo/seoextended/status_pager_num_for_title';
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
        $pathFrom = 'mageworx_seo/seosuite/status_pager_num_for_description';
        $pathTo   = 'mageworx_seo/seoextended/status_pager_num_for_description';
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
        $pathFrom = 'mageworx_seo/seosuite/cut_title_prefix_and_suffix';
        $pathTo   = 'mageworx_seo/seoextended/cut_title_prefix_and_suffix';
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
        $pathFrom = 'mageworx_seo/seosuite/enable_dynamic_meta_title';
        $pathTo   = 'mageworx_seo/seosuite/status_dynamic_meta_title';
        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
                if(!$coreConfig->getValue()){
                    $coreConfig->setValue('off');
                }elseif($coreConfig->getValue()){
                    $coreConfig->setValue('on');
                }
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom   = 'mageworx_seo/seosuite/status_dynamic_meta_title';
        $pathTo     = 'mageworx_seo/seoextended/status_dynamic_meta_title';
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
        $pathFrom = 'mageworx_seo/seosuite/enable_dynamic_meta_desc';
        $pathTo   = 'mageworx_seo/seosuite/status_dynamic_meta_desc';
        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                if(!$coreConfig->getValue()){
                    $coreConfig->setValue('off');
                }elseif($coreConfig->getValue()){
                    $coreConfig->setValue('on');
                }
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seosuite/status_dynamic_meta_desc';
        $pathTo   = 'mageworx_seo/seoextended/status_dynamic_meta_desc';
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
        $pathFrom = 'mageworx_seo/seosuite/enable_dynamic_meta_keywords';
        $pathTo   = 'mageworx_seo/seosuite/status_dynamic_meta_keywords';
        $collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                if(!$coreConfig->getValue()){
                    $coreConfig->setValue('off');
                }elseif($coreConfig->getValue()){
                    $coreConfig->setValue('on');
                }
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/seosuite/status_dynamic_meta_keywords';
        $pathTo   = 'mageworx_seo/seoextended/status_dynamic_meta_keywords';
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
        $pathFrom = 'mageworx_seo/seosuite/extended_category_layered_navigation_meta_title';
        $pathTo   = 'mageworx_seo/seoextended/extended_category_layered_navigation_meta_title';
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
        $pathFrom = 'mageworx_seo/seosuite/extended_category_layered_navigation_meta_description';
        $pathTo   = 'mageworx_seo/seoextended/extended_category_layered_navigation_meta_description';
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