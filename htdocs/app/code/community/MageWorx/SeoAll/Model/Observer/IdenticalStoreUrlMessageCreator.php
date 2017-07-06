<?php

class MageWorx_SeoAll_Model_Observer_IdenticalStoreUrlMessageCreator
{
    /**
     * Send message about identical store view URLs on mageworx routers
     *
     * @param $observer
     * @return null
     */
    public function sendMessage($observer)
    {
        if (strpos(Mage::helper('mageworx_seoall/request')->getCurrentFullActionName() , 'adminhtml_mageworx') !== 0) {
            return null;
        }

        $storeBaseUrls = array();

        foreach (Mage::helper('mageworx_seoall/store')->getAllEnabledStore() as $store) {
            $storeBaseUrls[] = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        }

        $uniqueStoreBaseUrls = array_unique($storeBaseUrls);

        if (count($uniqueStoreBaseUrls) == count($storeBaseUrls)) {
            return false;
        }

        $notification = Mage::getModel('adminnotification/inbox');

        if(is_object($notification) && ($notification instanceof Mage_Core_Model_Abstract)){
            $issetMessage = $notification->getCollection()->addFieldToFilter(
                'title',
                array(
                    'in' => array($this->_getTitle())
                )
            )->count();
            if (!$issetMessage) {
                if(method_exists($notification, 'addMinor')){
                    $notification->addMinor($this->_getTitle(), $this->_getMessage(), $this->_getUrl());
                }
            }
        }
    }

    /**
     *
     * @return string
     */
    protected function _getTitle()
    {
        //We don't translate for avoid duplicate messages.
        return "Mageworx: SEO problem (identical store URLs).";
    }

    /**
     * @return string
     */
    protected function _getUrl()
    {
        return "http://support.mageworx.com/extensions/seo_suite_pro_and_ultimate/the_same_url_keys_for_different_websitesstore_views.html";
    }

    /**
     * @return string
     */
    protected function _getMessage()
    {
        $helper = Mage::helper('mageworx_seoall');

        $partNotice = $helper->__('You have some stores with identical URLs. It can cause issues while indexing these URLs.');

        $link = " <a href = '" . $this->_getUrl() . "'> ";
        $notice = $helper->__("Please check") .  $link . $helper->__('here') . '</a> ' . $helper->__('for more details') . '.';

        return $partNotice . $notice;
    }
}