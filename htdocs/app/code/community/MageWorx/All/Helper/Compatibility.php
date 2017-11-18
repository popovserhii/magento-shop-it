<?php
/**
 * MageWorx
 * MageWorx All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Helper_Compatibility extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @param string $extensionDir
     * @param string $vendorDir
     * @return boolean
     */
    public function isDuplicateCodePool($extensionDir, $vendorDir = 'MageWorx')
    {
        if (!Mage::getStoreConfigFlag('mageworx/suppress_local_pool_notice/' . $vendorDir . '_' . $extensionDir)
            && is_file($this->getExtensionDirInLocalScope($extensionDir, $vendorDir))
            && is_file($this->getExtensionDirInCommunityScope($extensionDir, $vendorDir))
        ) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $extensionDir
     * @param string $vendorDir
     * @return string
     */
    public function getExtensionDirInLocalScope($extensionDir, $vendorDir = 'MageWorx')
    {
        return $this->_getExtensionDirInScope($extensionDir, $vendorDir, 'local');
    }

    /**
     *
     * @param string $extensionDir
     * @param string $vendorDir
     * @return string
     */
    public function getExtensionDirInCommunityScope($extensionDir, $vendorDir = 'MageWorx')
    {
        return $this->_getExtensionDirInScope($extensionDir, $vendorDir, 'community');
    }

    /**
     *
     * @param string $extensionDir
     * @param string $vendorDir
     * @param string $scope
     * @return string
     */
    protected function _getExtensionDirInScope($extensionDir, $vendorDir, $scope)
    {
        $path = array(
            Mage::getBaseDir('code'),
            $scope,
            $vendorDir,
            $extensionDir,
            'etc',
            'config.xml'
        );

        return implode(DS, $path);
    }
}
