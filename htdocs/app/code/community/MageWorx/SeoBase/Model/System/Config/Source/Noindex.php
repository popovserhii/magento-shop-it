<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_Noindex
{

    protected $_options;

    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $this->_options = array(
                array('value' => '^checkout_.+', 'label' => Mage::helper('mageworx_seobase')->__('Checkout Pages')),
                array('value' => '^contacts_.+', 'label' => Mage::helper('mageworx_seobase')->__('Contact Us Page')),
                array('value' => '^customer_.+', 'label' => Mage::helper('mageworx_seobase')->__('Customer Account Pages')),
                array('value' => '^catalog_product_compare_.+', 'label' => Mage::helper('mageworx_seobase')->__('Product Compare Pages')),
                //array('value'=>'^review.+', 'label'=> Mage::helper('mageworx_seobase')->__('Product Review Pages')),
                array('value' => '^rss_.+', 'label' => Mage::helper('mageworx_seobase')->__('RSS Feeds')),
                array('value' => '^catalogsearch_.+', 'label' => Mage::helper('mageworx_seobase')->__('Search Pages')),
                array('value' => '.*?_product_send$', 'label' => Mage::helper('mageworx_seobase')->__('Send Product Pages')),
                array('value' => '^tag_.+', 'label' => Mage::helper('mageworx_seobase')->__('Tag Pages')),
                array('value' => '^wishlist_.+', 'label' => Mage::helper('mageworx_seobase')->__('Wishlist Pages')),
            );
        }

        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }

}