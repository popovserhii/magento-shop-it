<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Review_View extends Mage_Review_Block_View
{

    public function __construct()
    {
        parent::__construct();
        if (!Mage::registry('current_product')) {
            Mage::register('current_product', $this->getProductData());
        }
    }

    public function getBackUrl()
    {
        if (Mage::helper('mageworx_seobase')->isReviewFriendlyUrlEnable()) {
            return Mage::getUrl(implode('/', array($this->getProductData()->getUrlKey(), 'reviews')));
        }
        else {
            return parent::getBackUrl();
        }
    }

}