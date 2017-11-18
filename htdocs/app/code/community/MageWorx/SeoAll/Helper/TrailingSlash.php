<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_TrailingSlash extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @param string $entityType
     * @param string $url
     * @param int|null $storeId
     * @return string
     */
    public function trailingSlash($entityType, $url, $storeId = null)
    {
        $trailingSlashFactory = Mage::getSingleton('mageworx_seoall/urlRenderer_trailingSlashFactory');
        return $trailingSlashFactory->getTrailingSlashModel($entityType)->trailingSlash($url, $storeId);
    }
}