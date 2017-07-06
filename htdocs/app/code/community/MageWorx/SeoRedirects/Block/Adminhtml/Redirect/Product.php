<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Product extends Mage_Adminhtml_Block_Template
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('mageworx_seoredirects/adminhtml_redirect_product_grid', 'redirect.product.grid'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve HTML of add button
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    /**
     * Get grid HTML
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}