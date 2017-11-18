<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoBase_Model_Hreflang_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * @var MageWorx_SeoAll_Helper_TrailingSlash
     */
    protected $_helperTrailingSlash;

    /**
     * @return array
     */
    abstract protected function _getHreflangUrls();

    /**
     * @return array
     */
    public function getHreflangUrls()
    {
        $this->_helperTrailingSlash = Mage::helper('mageworx_seoall/trailingSlash');
        $urls = $this->_getHreflangUrls();
        return $this->_cropUrlParams($urls);
    }

    protected function _cropUrlParams($urls)
    {
        if (!$urls) {
            return null;
        }

        foreach ($urls as $code => $url) {
            $pos = strpos($url, '?');
            $urls[$code] = $pos ? substr($urls[$code], 0, $pos) : $urls[$code];
        }
        return $urls;
    }

    /**
     * @param int $storeId
     * @return boolean
     */
    protected function _issetCrossDomainStore($storeId)
    {
        $crossDomainStoreId = Mage::helper('mageworx_seobase')->getCrossDomainStoreId($storeId);

        if ($crossDomainStoreId) {
            return true;
        }

        return false;
    }
}