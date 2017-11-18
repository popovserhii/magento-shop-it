<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('template_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Extended SEO Templates'));
    }

    /**
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $itemType = Mage::helper('mageworx_seoxtemplates/factory')->getItemType();

        if(Mage::helper('mageworx_seoxtemplates')->getStep() == 'new_step_1'){
            $this->addTab('settigs_tab',
                array(
                'label'   => Mage::helper('catalog')->__('Settings'),
                'title'   => Mage::helper('catalog')->__('Settings'),
                'content' => $this->getLayout()->createBlock("mageworx_seoxtemplates/adminhtml_template_{$itemType}_edit_tab_settings")->toHtml(),
                'active'  => true,
            ));
        }else{
            $this->addTab('general_tab',
                array(
                'label'   => Mage::helper('catalog')->__('Template'),
                'title'   => Mage::helper('catalog')->__('Template'),
                'content' => $this->getLayout()->createBlock("mageworx_seoxtemplates/adminhtml_template_{$itemType}_edit_tab_general")->toHtml(),
                'active'  => true,
            ));

            $this->addTab('condition', array(
                'label'   => $this->_getConditionLabel(),
                'content' => $this->getLayout()->createBlock("mageworx_seoxtemplates/adminhtml_template_{$itemType}_edit_tab_conditions")->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}