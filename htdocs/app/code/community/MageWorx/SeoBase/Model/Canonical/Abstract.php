<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoBase_Model_Canonical_Abstract extends Mage_Core_Model_Abstract
{
    /**
     *
     * @var MageWorx_SeoAll_Helper_TrailingSlash
     */
    protected $_helperTrailingSlash;

    /**
     *
     * @var MageWorx_SeoBase_Helper_Data
     */
    protected $_helperData;

    /**
     *
     * @var MageWorx_SeoAll_Helper_Store
     */
    protected $_helperStore;

    /**
     * @var array
     */
    protected $_canonicalUrls = array();

    /**
     * @param Mage_Catalog_Model_Abstract|null $item
     * @param int $storeId
     * @return string
     */
    abstract protected function _getCanonicalUrl($item = null, $storeId = null);

    /**
     *
     * @param Mage_Catalog_Model_Abstract|null $item
     * @return string
     */
    public function getCanonicalUrl($item = null, $storeId = null)
    {
        $storeId = $storeId ? $storeId : Mage::app()->getStore()->getStoreId();

        if (!empty($this->_canonicalUrls[$storeId])) {
            return $this->_canonicalUrls[$storeId];
        }

        $this->_helperData          = Mage::helper('mageworx_seobase');
        $this->_helperStore         = Mage::helper('mageworx_seoall/store');
        $this->_helperTrailingSlash = Mage::helper('mageworx_seoall/trailingSlash');

        if ($this->isCancelCanonical($storeId)) {
            return false;
        }

        $this->_canonicalUrls[$storeId] = str_ireplace('&amp;', '&', $this->_getCanonicalUrl($item, $storeId));

        return $this->_canonicalUrls[$storeId];
    }

    /**
     *
     * @param string $url
     * @return string
     */
    protected function _trailingSlash($url, $storeId = null)
    {
        return $this->_helperTrailingSlash->trailingSlash($this->_entityType, $url, $storeId);
    }

    /**
     * Check if cancel adding canonical URL by config settings
     * @param int $storeId
     * @return bool
     */
    protected function isCancelCanonical($storeId)
    {
        if (Mage::helper('mageworx_seobase')->isCanonicalUrlEnabled($storeId)) {
            $helperData = Mage::helper('mageworx_seobase');
            return in_array($helperData->getCurrentFullActionName(), $helperData->getCanonicalIgnorePages());
        }
        return true;
    }

    /**
     * Prepare ULR to output
     *
     * @param string $url
     * @param int|null $storeId
     * @return string
     */
    public function renderUrl($url, $storeId = null)
    {
        $url = $this->_deleteParametrs($url, array('___SID'));
        return $this->escapeUrl($this->_trailingSlash($url, $storeId));
    }

    /**
     * Prepare ULR to output
     *
     * @param string $url
     * @return string
     */
    public function escapeUrl($url)
    {
        return htmlspecialchars($url, ENT_COMPAT, 'UTF-8', false);
    }

    protected function _cropGetParameters($url)
    {
        if (strpos($url, '?') !== false) {
            list($cropedUrl) = explode('?', $url);
            return $cropedUrl;
        }
        return $url;
    }

    protected function _deleteSortParameters($url, $toolbar)
    {
    	if (is_object($toolbar)) {
    		$orderVarName     = $toolbar->getOrderVarName();
			$directionVarName = $toolbar->getDirectionVarName();
			$modeVarName      = $toolbar->getModeVarName();
    	}

        $orderVarName     = (!empty($orderVarName)) ? $orderVarName : 'order';
        $directionVarName = (!empty($directionVarName)) ? $directionVarName : 'dir';
        $modeVarName      = (!empty($modeVarName)) ? $modeVarName : 'mode';

        return $this->_deleteParametrs($url, array($orderVarName, $directionVarName, $modeVarName));
    }

    protected function _deleteLimitParameter($url, $toolbar)
    {
        $limitVarName = $toolbar->getLimitVarName() ? $toolbar->getLimitVarName() : 'limit';

        return $this->_deleteParametrs($url, array($limitVarName));
    }

    protected function _addLimitAllToUrl($url, $toolbar)
    {
        $limitVarName = $toolbar->getLimitVarName() ? $toolbar->getLimitVarName() : 'limit';

        if (strpos($url, '?') !== false) {
            $url = $url . '&' . $limitVarName . '=all';
        }
        else {
            $url = $url . '?' . $limitVarName . '=all';
        }
        return $url;
    }

    protected function _deleteParametrs($url, array $cropParams)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }

        $params = array();
        parse_str(html_entity_decode($parseUrl['query']), $params);

        foreach ($cropParams as $cropName) {
            if (array_key_exists($cropName, $params)) {
                unset($params[$cropName]);
            }
        }

        $queryString = '';
        foreach ($params as $name => $value) {
            if ($queryString == '') {
                $queryString = '?' . $name . '=' . $value;
            }
            else {
                $queryString .= '&' . $name . '=' . $value;
            }
        }

        $url = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'] . $queryString;
        return $url;
    }

    public function _deletePagerParameter($url, $toolbar)
    {
        $pageVarName = $toolbar->getPageVarName();
        $url         = $this->_deleteParametrs($url, array($pageVarName));

        return $url;
    }

    /**
     *
     * @param int|null $storeId
     * @param object|null $entity Object must provide getCanonicalCrossDomain() method
     * @return int|null
     */
    protected function _getCrossDomainStoreId($storeId = null, $entity = null)
    {
        if (is_object($entity) && is_callable(array($entity, 'getCanonicalCrossDomain'))) {
            $personalCrossDomainStoreId = $entity->getCanonicalCrossDomain();

            if ($this->_isValidCrossDomainStoreId($personalCrossDomainStoreId, $storeId)) {
                return $personalCrossDomainStoreId;
            }
        }

        $configCrossDomainStoreId = $this->_helperData->getCrossDomainStoreId($storeId);

        if ($this->_isValidCrossDomainStoreId($configCrossDomainStoreId, $storeId)) {
            return $configCrossDomainStoreId;
        }

        return null;
    }

    /**
     * Check if store ID is valid
     *
     * @param int $storeId
     * @return int|false
     */
    protected function _isValidCrossDomainStoreId($crossDomainStoreId, $storeId)
    {
        if (!$crossDomainStoreId) {
            return false;
        }
        if (!$this->_helperStore->isActiveStore($crossDomainStoreId)) {
            return false;
        }

        if ($storeId == $crossDomainStoreId) {
            return false;
        }
        return true;
    }

}