<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Source_Reference
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
                array('value' => 'ref_static_url',   'label' => $helper->__('Custom URL')),
                array('value' => 'ref_product_sku',  'label' => $helper->__('To Product by SKU')),
                array('value' => 'ref_category_id',  'label' => $helper->__('To Category by ID')),
            );
        }
        $options = $this->_options;
        return $options;
    }
}