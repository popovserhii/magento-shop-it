<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_FriendlyReview extends Mage_Core_Model_Abstract
{
    static protected $_singleton = array();

    public function replaceReviewPagerUrl($observer)
    {
        if (!Mage::helper('mageworx_seobase')->isReviewFriendlyUrlEnable()) {
            return;
        }

        if (Mage::helper('mageworx_seobase')->getCurrentFullActionName() != 'review_product_list') {
            return;
        }

        $block = $observer->getBlock();
        if (is_object($block) && $block instanceof Mage_Page_Block_Html_Pager) {
            if (!$this->_observerCanRun(__METHOD__)) {
                return;
            }

            $output = $observer->getTransport()->getHtml();

            preg_match_all('#<a[^>]*>#is', $output, $hrefParts);

            if (!empty($hrefParts[0])){
                foreach ($hrefParts[0] as $hrefPart) {
                    if(strpos($hrefPart, '/reviews') !== false){
                        $replacedhrefPart = str_replace('/reviews', Mage::app()->getRequest()->getOriginalPathInfo(), $hrefPart);
                        $output = str_replace($hrefPart, $replacedhrefPart, $output);
                    }
                }
            }

            preg_match_all('#<option[^>]*>#is', $output, $optionParts);
            if (!empty($optionParts[0])){
                foreach ($optionParts[0] as $optionPart) {
                    if(strpos($optionPart, '/reviews') !== false){
                        $replacedOptionPart = str_replace('/reviews', Mage::app()->getRequest()->getOriginalPathInfo(), $optionPart);
                        $output = str_replace($optionPart, $replacedOptionPart, $output);
                    }
                }
            }

            $observer->getTransport()->setHtml($output);
        }
    }

    protected function _observerCanRun($method)
    {
        if (!isset(self::$_singleton[$method])) {
            self::$_singleton[$method] = true;
            return true;
        }

        return false;
    }

}