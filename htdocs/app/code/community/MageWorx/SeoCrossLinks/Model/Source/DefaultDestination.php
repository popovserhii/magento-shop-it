<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Source_DefaultDestination
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
                array('value' => 'product_page',       'label' => $helper->__('Product Page')),
                array('value' => 'category_page',      'label' => $helper->__('Category Page')),
                array('value' => 'cms_page_content',   'label' => $helper->__('CMS Page Content')),
                array('value' => 'blog_content',       'label' => $helper->__('AW Blog')),
            );
        }

        $options = $this->_options;
        return $options;
    }
}