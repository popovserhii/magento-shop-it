<?php
/**
 * MageWorx
 * MageWorx SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Version extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @return boolean
     */
    public function isEeRewriteActive()
    {
        return ('true' == (string) Mage::getConfig()->getNode('modules/Enterprise_UrlRewrite/active'));
    }
}