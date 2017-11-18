<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoReports_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_MAX_LENGTH_META_TITLE       = 'mageworx_seo/seoreports/max_title_length';
    const XML_PATH_MAX_LENGTH_META_DESCRIPTION = 'mageworx_seo/seoreports/max_description_length';
    const MAX_LENGTH_META_TITLE                = 70;
    const MAX_LENGTH_META_DESCRIPTION          = 150;

    public function getMaxLengthMetaTitle()
    {
        $max = (int)Mage::getStoreConfig(self::XML_PATH_MAX_LENGTH_META_TITLE);
        if(!$max){
            return self::MAX_LENGTH_META_TITLE;
        }
        return $max;
    }

    public function getMaxLengthMetaDescription()
    {
        $max = (int)Mage::getStoreConfig(self::XML_PATH_MAX_LENGTH_META_DESCRIPTION);
        if(!$max){
            return self::MAX_LENGTH_META_DESCRIPTION;
        }
        return $max;
    }

}