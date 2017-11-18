<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Block_Markup_Category extends Mage_Core_Block_Template
{
    protected function _getMarkupHtml()
    {
        $head = $this->getLayout()->getBlock('head');
        if (!is_object($head)) {
            return '';
        }

        if (!($head instanceof Mage_Page_Block_Html_Head)) {
            return false;
        }

        $html = Mage::helper('mageworx_seomarkup/html_category')->getSocialCategoryInfo($head);
        return $html;
    }

    public function _toHtml()
    {
        return $this->_getMarkupHtml();
    }

}