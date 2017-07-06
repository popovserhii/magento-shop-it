<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Model_UrlRenderer_TrailingSlashFactory
{
    /**
     *
     * @param string $entityType
     * @return MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Abstract
     */
    public function getTrailingSlashModel($entityType)
    {
        $entityTypeList = array(
            'product',
            'category',
            'page',
            'home',
            'review',
            'tag',
            'default'
        );

        if (!in_array($entityType, $entityTypeList)) {
            $entityType = 'default';
        }
        $uri = 'mageworx_seoall/urlRenderer_trailingSlash_' . $entityType;

        return Mage::getSingleton($uri);
    }
}