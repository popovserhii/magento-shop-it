<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Tag extends MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Abstract
{
    /**
     *
     * {@inheritDoc}
     */
    protected function _getDefaultTrailingSlashMethod()
    {
        return self::TRAILING_SLASH_CROP;
    }

}