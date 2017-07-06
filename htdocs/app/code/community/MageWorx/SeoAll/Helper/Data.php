<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_TRAILING_SLASH           = 'mageworx_seo/seoall/trailing_slash';
    const XML_PATH_TRAILING_SLASH_HOME_PAGE = 'mageworx_seo/seoall/trailing_slash_home_page';

    /**
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTrailingSlashAction($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TRAILING_SLASH, $storeId);
    }

    /**
     *
     * @param int $storeId
     * @return boolean
     */
    public function cropTrailingSlashForHomePageUrl($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_TRAILING_SLASH_HOME_PAGE, $storeId);
    }
}