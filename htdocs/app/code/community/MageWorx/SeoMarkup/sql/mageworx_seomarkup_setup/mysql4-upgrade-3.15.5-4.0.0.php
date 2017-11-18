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

///Transfer settings
$collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', 'mageworx_seo/richsnippets/enable');
if ($collection->count() > 0) {

    try {
        $pathToAll               = MageWorx_SeoMarkup_Helper_Config::XML_PATH_ENABLED;
        $pathToProduct           = MageWorx_SeoMarkup_Helper_Config::XML_PATH_PRODUCT_ENABLED;
        $pathToProductMethod     = MageWorx_SeoMarkup_Helper_Config::XML_PATH_PRODUCT_METHOD;
        $pathToBreadcrumbs       = MageWorx_SeoMarkup_Helper_Config::XML_PATH_BREADCRUMBS_ENABLED;
        $pathToBreadcrumbsMethod = MageWorx_SeoMarkup_Helper_Config::XML_PATH_BREADCRUMBS_METHOD;

        if ($collection->count() > 0) {
            foreach ($collection as $coreConfig) {
                if ($coreConfig->getValue() === '2') {
                    ///Only breadcrumbs richsnippets
                    $configAll = new Mage_Core_Model_Config();
                    $configAll->saveConfig($pathToAll, 1, $coreConfig->getScope(), $coreConfig->getScopeId());

                    $configProduct = new Mage_Core_Model_Config();
                    $configProduct->saveConfig($pathToProduct, 1, $coreConfig->getScope(), $coreConfig->getScopeId());

                    $configProductMethod = new Mage_Core_Model_Config();
                    $configProductMethod->saveConfig(
                        $pathToProductMethod,
                        MageWorx_SeoMarkup_Model_System_Config_Source_ProductMethod::RICHSNIPPET_INJECTION_MICRODATA,
                        $coreConfig->getScope(),
                        $coreConfig->getScopeId()
                    );

                    $coreConfig->delete();

                } elseif ($coreConfig->getValue() === '1') {
                    ///Breadcrumbs + Product richsnippets
                    $configAll = new Mage_Core_Model_Config();
                    $configAll->saveConfig($pathToAll, 1, $coreConfig->getScope(), $coreConfig->getScopeId());

                    $configProduct = new Mage_Core_Model_Config();
                    $configProduct->saveConfig($pathToProduct, 1, $coreConfig->getScope(), $coreConfig->getScopeId());

                    $configProductMethod = new Mage_Core_Model_Config();
                    $configProductMethod->saveConfig(
                        $pathToProductMethod,
                        MageWorx_SeoMarkup_Model_System_Config_Source_ProductMethod::RICHSNIPPET_INJECTION_MICRODATA,
                        $coreConfig->getScope(),
                        $coreConfig->getScopeId()
                    );

                    $configBreadcrumbs = new Mage_Core_Model_Config();
                    $configBreadcrumbs->saveConfig(
                        $pathToBreadcrumbs,
                        1,
                        $coreConfig->getScope(),
                        $coreConfig->getScopeId()
                    );

                    $configBreadcrumbsMethod = new Mage_Core_Model_Config();
                    $configBreadcrumbsMethod->saveConfig(
                        $pathToBreadcrumbsMethod,
                        MageWorx_SeoMarkup_Model_System_Config_Source_BreadcrumbsMethod::RICHSNIPPET_INJECTION_MICRODATA,
                        $coreConfig->getScope(),
                        $coreConfig->getScopeId()
                    );

                    $coreConfig->delete();
                } else {
                    $coreConfig->delete();
                }
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    try {
        $pathFrom = 'mageworx_seo/richsnippets/product_og_enabled';
        $pathTo   = MageWorx_SeoMarkup_Helper_Config::XML_PATH_PRODUCT_OPENGRAPH_ENABLED;
        $collectionOG = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $pathFrom);
        if ($collectionOG->count() > 0) {
            foreach ($collectionOG as $coreConfig) {
                $coreConfig->setPath($pathTo)->save();
            }
        }
    } catch (Exception $e) {
        Mage::log($e->getMessage(), Zend_Log::ERR);
    }

    $installer->run("DELETE FROM {$this->getTable('core_resource')} WHERE code = 'seomarkup_setup' LIMIT 1");

}

$installer->endSetup();