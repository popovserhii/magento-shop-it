<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Block_Adminhtml_Crosslink extends Mage_Adminhtml_Block_Template
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('mageworx_seocrosslinks')->__('New Cross Link'),
                'onclick' => "setLocation('" . $this->getUrl('*/*/new') . "')",
                'class' => 'add'
            ))
        );

        $this->setChild('grid', $this->getLayout()->createBlock('mageworx_seocrosslinks/adminhtml_crosslink_grid', 'crosslink.grid'));
        return parent::_prepareLayout();
    }

    /**
     * Retrive HTML of add button
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