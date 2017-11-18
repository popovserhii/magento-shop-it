<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoReports_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_SEOSUITE_PRODUCT_REPORT_STATUS  = 'mageworx_seo/seoreports/product_report_status';
    const XML_PATH_SEOSUITE_CATEGORY_REPORT_STATUS = 'mageworx_seo/seoreports/category_report_status';
    const XML_PATH_SEOSUITE_CMS_REPORT_STATUS      = 'mageworx_seo/seoreports/cms_report_status';

    protected $_enterpriseSince113;

    public function setProductReportStatus($flag)
    {
        Mage::getConfig()->saveConfig(self::XML_PATH_SEOSUITE_PRODUCT_REPORT_STATUS, $flag);
    }

    public function getProductReportStatus()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEOSUITE_PRODUCT_REPORT_STATUS);
    }

    public function setCategoryReportStatus($flag)
    {
        Mage::getConfig()->saveConfig(self::XML_PATH_SEOSUITE_CATEGORY_REPORT_STATUS, $flag);
    }

    public function getCategoryReportStatus()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEOSUITE_CATEGORY_REPORT_STATUS);
    }

    public function setCmsReportStatus($flag)
    {
        // if doesn't work, check save new row in DB!!!!!!!!
        Mage::getConfig()->saveConfig(self::XML_PATH_SEOSUITE_CMS_REPORT_STATUS, $flag);
    }

    public function getCmsReportStatus()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SEOSUITE_CMS_REPORT_STATUS);
    }

    public function getErrorTypes($arr = array())
    {
        $errorTypes = array();
        if (empty($arr) || in_array('missing', $arr)) {
            $errorTypes['missing'] = $this->__('Missing');
        }
        if (empty($arr) || in_array('long', $arr)) {
            $errorTypes['long'] = $this->__('Long');
        }
        if (empty($arr) || in_array('duplicate', $arr)) {
            $errorTypes['duplicate'] = $this->__('Duplicate');
        }
        return $errorTypes;
    }

    public function _trimText($str)
    {
        if (!$str) {
            return '';
        }
        return trim(preg_replace("/\s+/uis", ' ', $str));
    }

    public function _prepareText($str)
    {
        if (!$str) {
            return '';
        }
        $str = strtolower(preg_replace("/[^\w\d]+/uis", ' ', $str));
        return $this->_trimText($str);
    }

    public function mbStrLenSafety($str)
    {
        if(function_exists('mb_strlen')){
            return mb_strlen($this->_trimText($str), 'UTF-8');
        }

        return strlen($this->_trimText($str));
    }

    public function getDefaultStoreId()
    {
        if(Mage::app()->isSingleStoreMode()){
            return  Mage::app()->getStore(true)->getId();
        }

        if(Mage::app()->getDefaultStoreView()){
            return Mage::app()->getDefaultStoreView()->getId();
        }

        return array_shift($this->getAllEnabledStore())->getStoreId();
    }

    /**
     *
     * @return array
     */
    public function getAllEnabledStore()
    {
        $allStores = Mage::app()->getStores();
        $stores    = array();
        foreach ($allStores as $store) {
            if ($store->getIsActive() == 1) {
                $stores[] = $store;
            }
        }
        return $stores;
    }

    public function isEnterpriseSince113()
    {
        if (is_null($this->_enterpriseSince113)) {
            $mage = new Mage();
            if (is_callable(array($mage, 'getEdition')) && Mage::getEdition() == Mage::EDITION_ENTERPRISE
                && version_compare(Mage::getVersion(), '1.13.0.0', '>=')) {
                $this->_enterpriseSince113 = true;
            }
            else {
                $this->_enterpriseSince113 = false;
            }
        }
        return $this->_enterpriseSince113;
    }
}