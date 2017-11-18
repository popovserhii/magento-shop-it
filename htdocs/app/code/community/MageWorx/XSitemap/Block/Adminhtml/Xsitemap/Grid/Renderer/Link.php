<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Block_Adminhtml_Xsitemap_Grid_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $fileName = preg_replace('/^\//', '', $row->getSitemapPath() . $row->getSitemapFilename());
//        $code = Mage::app()->getStore($row->getStoreId())->getCode();
//        $url = $this->htmlEscape(str_replace('/index.php', '', Mage::app()->getStore($row->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK)) . $fileName);
        $url      = $this->htmlEscape(str_replace('/index.php', '',
                Mage::app()->getStore(0)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK)) . $fileName);
        if (file_exists(BP . DS . $fileName)) {
            return sprintf('<a href="%1$s" target = "_blank">%1$s</a>', $url);
        }
        return $url;
    }

}
