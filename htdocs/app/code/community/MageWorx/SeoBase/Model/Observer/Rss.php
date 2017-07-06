<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_Rss extends Mage_Core_Model_Abstract
{
    public function replaceCategoryRssLink($observer)
    {
        if (Mage::helper('mageworx_seobase')->getCurrentFullActionName() != 'catalog_category_view') {
            return;
        }

        $block = $observer->getBlock();
        if (is_object($block) && $block->getNameInLayout() == 'category.products') {
            $output = $observer->getTransport()->getHtml();
            if ($output) {
                $friendlyUrl = Mage::getUrl('rss/' . Mage::app()->getStore()->getCode() . '/' . $block->getCurrentCategory()->getUrlKey());
                $output = str_replace($block->getRssLink(), $friendlyUrl, $output);
            }
            $observer->getTransport()->setHtml($output);
        }
    }
}