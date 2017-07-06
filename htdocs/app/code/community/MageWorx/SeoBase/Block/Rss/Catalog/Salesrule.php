<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Rss_Catalog_Salesrule extends Mage_Rss_Block_Catalog_Salesrule
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_salesrule_'.$this->getStoreId().'_'.$this->_getCustomerGroupId());
        $this->setCacheLifetime(0);
    }


    protected function _toHtml()
    {
        //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        //customer group id
        $custGroup =   $this->_getCustomerGroupId();

        $newurl = Mage::helper('core/url')->getCurrentUrl();
        $title = Mage::helper('rss')->__('%s - Discounts and Coupons', sprintf('%s (%s)', Mage::app()->getStore()->getGroup()->getName(), Mage::app()->getStore($storeId)->getName()));
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'generator'   => Mage::helper('mageworx_seobase')->getRssGenerator(),
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $now = date('Y-m-d');
        $_saleRule = Mage::getModel('salesrule/rule');

        $collection = $_saleRule->getResourceCollection();

        /**
         * In magento 1.7+ Mage_SalesRule_Model_Resource_Rule_Collection has addWebsiteGroupDateFilter()
         */
        if ( is_callable(array($collection, "addWebsiteGroupDateFilter"))) {
            $collection->addWebsiteGroupDateFilter($websiteId, $custGroup);
        } else {
            $collection->addFieldToFilter('website_ids',array('finset' => $websiteId));
            $collection->addFieldToFilter('customer_group_ids', array('finset' => $custGroup));
        }

        $collection->addFieldToFilter('from_date', array('date'=>true, 'to' => $now))
                    ->addFieldToFilter('is_rss',1)
                    ->addFieldToFilter('is_active',1)
                    ->setOrder('from_date','desc');
        $collection->getSelect()->where('to_date is null or to_date>=?', $now);

        foreach ($collection as $sr) {
            $description = '<table><tr>'.
            '<td style="text-decoration:none;">'.$sr->getDescription().
            '<br/>Discount Start Date: '.$this->formatDate($sr->getFromDate(), 'medium').
            ( $sr->getToDate() ? ('<br/>Discount End Date: '.$this->formatDate($sr->getToDate(), 'medium')):'').
            ($this->escapeHtml($sr->getCouponCode()) ? '<br/> Coupon Code: '.$this->escapeHtml($sr->getCouponCode()).'' : '').
            '</td>'.
            '</tr></table>';
             $data = array(
                'title'         => $sr->getName(),
                'description'   => $description,
                'link'          => Mage::getUrl(''),
                );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}