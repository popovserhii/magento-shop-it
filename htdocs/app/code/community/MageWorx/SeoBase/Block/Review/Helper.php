<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Review_Helper extends MageWorx_SeoBase_Block_Review_Helper_Abstract
{
    /**
     * @return string
     */
    public function getReviewsUrl()
    {
        if (Mage::helper('mageworx_seobase')->isReviewFriendlyUrlEnable()) {

            $path = array(
                $this->getReviewUrlPart(),
                'reviews'
            );

            return Mage::getUrl(implode('/', $path));
        }

        return parent::getReviewsUrl();
    }


    public function _toHtml()
    {
        if (Mage::getStoreConfigFlag('advanced/modules_disable_output/Mage_Review')) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    protected function getReviewUrlPart()
    {
        if (Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            return $this->getProduct()->getUrlKey();
        }

        $urlPath = $this->getProduct()->getUrlPath();
        if (!$urlPath) {
            return $this->getProduct()->getUrlKey();
        }

        $productUrlSeoSuffix = Mage::helper('catalog/product')->getProductUrlSuffix();
        return Mage::helper('mageworx_seoall/string')->cropLastPart($urlPath, array($productUrlSeoSuffix, '.', '/'), true);
    }
}