<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


/**
 * @see MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Breadcrumbs
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Breadcrumbs_Crumb_Last extends MageWorx_SeoMarkup_Model_Richsnippet_Breadcrumbs_Crumb
{
    protected function _getItemConditions()
    {
        return false;
    }

    protected function _getItemValues()
    {
        return Mage::helper('mageworx_seomarkup')->getCurrentEntityNameList();
    }
}