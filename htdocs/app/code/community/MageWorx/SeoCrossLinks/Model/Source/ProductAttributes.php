<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Source_ProductAttributes
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
                array('value' => 'short_description', 'label' => $helper->__('Product Short Description')),
                array('value' => 'description',       'label' => $helper->__('Product Description  ')),
            );
        }
        $options = $this->_options;
        return $options;
    }
}