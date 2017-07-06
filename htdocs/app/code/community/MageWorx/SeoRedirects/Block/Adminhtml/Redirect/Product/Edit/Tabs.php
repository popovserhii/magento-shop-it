<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('redirect_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('SEO Product Redirects'));
    }

    /**
     * Add tabs to edit page
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_tab',
            array(
            'label'   => Mage::helper('catalog')->__('General'),
            'title'   => Mage::helper('catalog')->__('General'),
            'content' => $this->getLayout()->createBlock("mageworx_seoredirects/adminhtml_redirect_product_edit_tab_general")->toHtml(),
            'active'  => true,
        ));

        return parent::_beforeToHtml();
    }
}