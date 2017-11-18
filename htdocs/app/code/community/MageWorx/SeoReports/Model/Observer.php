<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Model_Observer
{
    public function productSaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('seoreports')->getProductReportStatus()) Mage::helper('seoreports')->setProductReportStatus(0);
    }

    public function categorySaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('seoreports')->getCategoryReportStatus()) Mage::helper('seoreports')->setCategoryReportStatus(0);
    }

    public function cmsPageSaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::helper('seoreports')->getCmsReportStatus()) Mage::helper('seoreports')->setCmsReportStatus(0);
    }
}