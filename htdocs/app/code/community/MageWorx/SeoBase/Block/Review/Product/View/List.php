<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Review_Product_View_List extends Mage_Review_Block_Product_View_List
{

    public function getReviewUrl($id)
    {
        if (Mage::helper('mageworx_seobase')->isReviewFriendlyUrlEnable()) {
            $review         = Mage::getModel('review/review')->load($id);
            $formattedTitle = $this->getProduct()->formatUrlKey($review->getTitle());
            return Mage::getUrl(
                implode('/', array($this->getProduct()->getUrlKey(), 'reviews', $formattedTitle)),
                array('id' => $id)
            );
        }
        else {
            return parent::getReviewUrl($id);
        }
    }

}