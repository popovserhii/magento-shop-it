<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_NextPrev_Tag extends MageWorx_SeoBase_Model_NextPrev_Abstract
{
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
        $pager = Mage::app()->getLayout()->getBlock('product_list_toolbar_pager');

        return is_object($pager) ? $pager : null;
    }
}