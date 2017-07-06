<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Canonical_HomePage extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    /**
     *
     * @var string
     */
    protected $_entityType = 'home';

    /**
     *
     * @param Mage_Catalog_Model_Category|null $item
     * @param int|null $storeId
     * @return string
     */
    protected function _getCanonicalUrl($item = null, $store = null)
    {
        $crossDomainStoreId = $this->_getCrossDomainStoreId();

        if ($crossDomainStoreId) {
            $url = $this->renderUrl($this->_helperStore->getStoreBaseUrl($crossDomainStoreId), $crossDomainStoreId);
        } else {
            $url = $this->renderUrl(Mage::getBaseUrl());
        }

        return $url;
    }
}