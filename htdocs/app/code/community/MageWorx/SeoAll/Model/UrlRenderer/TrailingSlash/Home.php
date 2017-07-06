<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Home extends MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Abstract
{
    /**
     * {@inheritDoc}
     */
    public function trailingSlash($url, $storeId = null)
    {
        $this->_init();

        if ($this->_helperData->cropTrailingSlashForHomePageUrl($storeId)) {
            $url = $this->_cropTrailingSlash($url);
        }
        return $url;
    }
}