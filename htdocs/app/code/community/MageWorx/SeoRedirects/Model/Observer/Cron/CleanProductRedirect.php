<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Model_Observer_Cron_CleanProductRedirect extends Mage_Core_Model_Abstract
{
    /**
     * Delete old product redirects
     *
     * @return null
     */
    public function scheduledCleanRedirects()
    {
        $collection = Mage::getModel("mageworx_seoredirects/redirect_product")->getCollection();
        $collection->addDateFilter(Mage::helper('mageworx_seoredirects')->getCountStableDay());

        foreach ($collection as $model){
            $model->delete();
        }
    }
}