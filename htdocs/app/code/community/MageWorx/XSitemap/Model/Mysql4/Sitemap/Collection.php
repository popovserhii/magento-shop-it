<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Mysql4_Sitemap_Collection extends Mage_Sitemap_Model_Mysql4_Sitemap_Collection
{
    public function _construct()
    {
        $this->_init('xsitemap/sitemap');
    }

}
