<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Title extends Mage_Core_Helper_Abstract
{
    const XML_PATH_MAGENTO_TITLE_PREFIX  = "design/head/title_prefix";
    const XML_PATH_MAGENTO_TITLE_SUFFIX  = "design/head/title_suffix";

    /**
     * @return string
     */
    public function getMagentoTitlePrefix()
    {
        return Mage::getStoreConfig(self::XML_PATH_MAGENTO_TITLE_PREFIX);
    }

    /**
     * @return string
     */
    public function getMagentoTitleSuffix()
    {
        return Mage::getStoreConfig(self::XML_PATH_MAGENTO_TITLE_SUFFIX);
    }

    /**
     * @param string $title
     * @return string
     */
    public function cutPrefixSuffix($title)
    {
        $prefix = $this->getMagentoTitlePrefix();
        $suffix = $this->getMagentoTitleSuffix();

        if ($prefix && strpos($title, $prefix) !== false) {
            $title = trim(str_replace($prefix, '', $title));
        }
        if ($suffix && strpos($title, $suffix) !== false) {
            $title = trim(str_replace($suffix, '', $title));
        }

        return $title;
    }
}