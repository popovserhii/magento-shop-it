<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Helper_Compatibility extends Mage_Core_Helper_Abstract
{
    public function getExtensionDirInLocalScope()
    {
        return Mage::getBaseDir('code') . DS . 'local' . DS . 'MageWorx' . DS . 'XSitemap';
    }

    public function oldConfigXmlExists()
    {
        return is_file($this->getExtensionDirInLocalScope()  . DS . 'etc' . DS . 'config.xml');
    }
}
