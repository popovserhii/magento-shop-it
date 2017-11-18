<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Source_DefaultGridColumns
{
    protected $_options;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $helper = Mage::helper('mageworx_seocrosslinks');
            $this->_options = array(
                array('value' => 'link_title',        'label' => $helper->__('Link Title')),
                array('value' => 'link_target',       'label' => $helper->__('Link Target')),
                array('value' => 'store_id',          'label' => $helper->__('Store View')),
                array('value' => 'ref_static_url',    'label' => $helper->__('Custom URL')),
                array('value' => 'ref_product_sku',   'label' => $helper->__('Product SKU')),
                array('value' => 'ref_category_id',   'label' => $helper->__('Category ID')),
                array('value' => 'replacement_count', 'label' => $helper->__('Replacement Count')),
                array('value' => 'in_product',        'label' => $helper->__('Use in Product Page')),
                array('value' => 'in_category',       'label' => $helper->__('Use in Category Page')),
                array('value' => 'in_cms_page',       'label' => $helper->__('Use in CMS Page')),
                array('value' => 'in_blog',           'label' => $helper->__('Use in Blog Post Page')),
            );
        }
        $options = $this->_options;
        return $options;
    }
}