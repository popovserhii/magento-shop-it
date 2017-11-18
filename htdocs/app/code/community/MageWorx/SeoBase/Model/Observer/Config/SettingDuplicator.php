<?php

class MageWorx_SeoBase_Model_Observer_Config_SettingDuplicator
{
    
    public function duplicateConfigSettings(Varien_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfigData();

        if ($config && $config->getPath() == 'mageworx_seo/seobase/product_use_categories' && !$config->hasFlag('save_from_observer')) {

            $currentValue = Mage::getStoreConfigFlag('mageworx_seo/seobase/product_use_categories');

            $data = $config->getData();
            $configValue = $data['groups']['seobase']['fields']['product_use_categories']['value'];

            if ($currentValue != $configValue) {

                $mageConfig = Mage::getModel('core/config_data')->getCollection()
                    ->addFieldToFilter('scope', $config->getScope())
                    ->addFieldToFilter('scope_id', $config->getScopeId())
                    ->addFieldToFilter('path', 'catalog/seo/product_use_categories')
                    ->getFirstItem();

                $mageConfig
                    ->setScope($config->getScope())
                    ->setScopeId($config->getScopeId())
                    ->setPath('catalog/seo/product_use_categories')
                    ->setValue($config->getValue())
                    ->setFlag('save_from_observer')
                    ->save();

                $this->_showUseCategoriesPathMessage();
            }
        }
        elseif ($config && $config->getPath() == 'catalog/seo/product_use_categories' && !$config->hasFlag('save_from_observer')) {

            $currentValue = Mage::getStoreConfigFlag('catalog/seo/product_use_categories');
            $data = $config->getData();

            $configValue = $data['groups']['seo']['fields']['product_use_categories']['value'];

            if ($currentValue != $configValue) {

                $mageworxConfig = Mage::getModel('core/config_data')->getCollection()
                    ->addFieldToFilter('scope', $config->getScope())
                    ->addFieldToFilter('scope_id', $config->getScopeId())
                    ->addFieldToFilter('path', 'mageworx_seo/seobase/product_use_categories')
                    ->getFirstItem();

                $mageworxConfig
                    ->setScope($config->getScope())
                    ->setScopeId($config->getScopeId())
                    ->setPath('mageworx_seo/seobase/product_use_categories')
                    ->setValue($config->getValue())
                    ->setFlag('save_from_observer')
                    ->save();

                $this->_showUseCategoriesPathMessage();
            }
        }
    }

    protected function _showUseCategoriesPathMessage()
    {
        $message = Mage::helper('mageworx_seobase')->__('You\'ve changed the setting "Use Categories Path for Product URLs".
                    Please, pay attention that the dependent setting "SEO Suite -> Add Canonical URL Meta Header -> Product Canonical URL"
                    could change as well.');

        Mage::getSingleton('adminhtml/session')->addNotice($message);
    }
}