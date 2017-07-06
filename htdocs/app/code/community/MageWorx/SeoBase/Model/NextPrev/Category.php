<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_NextPrev_Category extends MageWorx_SeoBase_Model_NextPrev_Abstract
{
    const ENABLE_IF_NO_FILTERS = 2;

    /**
     * Retrive next page URL
     *
     * @return string
     */
    public function getNextUrl()
    {
        return $this->_initNextPrev()->_nextUrl;
    }

    /**
     * Retrive previous page URL
     *
     * @return string
     */
    public function getPrevUrl()
    {
        return $this->_initNextPrev()->_prevUrl;
    }

    /**
     * Retrive pager block from layout
     *
     * @return object
     */
    protected function _getPager()
    {
        if ($this->_helperData->getStatusLinkRel() == self::ENABLE_IF_NO_FILTERS
            && Mage::helper('mageworx_seoall/layeredFilter')->isApplyedLayeredNavigationFilters()
        ) {
            return null;
        }

        //Disable next/prev on category without product, if is not layered navigation now.
        if (is_object(Mage::registry('current_category')) &&
            Mage::registry('current_category')->getDisplayMode() == 'PAGE' &&
            !Mage::helper('mageworx_seoall/layeredFilter')->isApplyedLayeredNavigationFilters())
        {
            return null;
        }

        $pager = Mage::app()->getLayout()->getBlock('product_list_toolbar_pager');

        if (!is_object($pager) || !$pager->getCollection()) {
            $toolbar = Mage::app()->getLayout()->getBlock('product_list_toolbar');
            if (is_object($toolbar)) {
                $pager = $toolbar->getChild('product_list_toolbar_pager');
            }
        }

        return is_object($pager) ? $pager : null;
    }    

}

