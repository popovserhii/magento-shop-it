<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Block_Adminhtml_Crosslink_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('crosslink_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('SEO Cross Links'));
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
            'content' => $this->getLayout()->createBlock("mageworx_seocrosslinks/adminhtml_crosslink_edit_tab_general")->toHtml(),
            'active'  => true,
        ));

        $this->addTab('destination_tab', array(
            'label'   => Mage::helper('catalog')->__('Destination'),
            'title'   => Mage::helper('catalog')->__('Destination'),
            'content' => $this->getLayout()->createBlock("mageworx_seocrosslinks/adminhtml_crosslink_edit_tab_destination")->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}