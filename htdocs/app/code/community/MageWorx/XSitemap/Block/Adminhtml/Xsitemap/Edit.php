<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Block_Adminhtml_Xsitemap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'sitemap_id';
        $this->_blockGroup = 'xsitemap';
        $this->_controller = 'adminhtml_xsitemap';

        parent::__construct();

        $this->_addButton('generate',
            array(
            'label'   => Mage::helper('xsitemap')->__('Save & Generate'),
            'onclick' => "$('generate').value=1; editForm.submit();",
            'class'   => 'add',
        ));

        $message = "Are you sure you want to do this? Sitemap file will be remote if exists.";

        $this->_updateButton('save', null,
            array(
            'label'      => Mage::helper('xsitemap')->__('Save'),
            'onclick'    => 'confirm(\'' . Mage::helper('xsitemap')->__($message) . '\')? editForm.submit() : false',
            'class'      => 'save',
            'sort_order' => '',
        ));
    }

    public function getHeaderText()
    {
        if (Mage::registry('sitemap_sitemap')->getId()) {
            return Mage::helper('xsitemap')->__('Edit Sitemap');
        }
        else {
            return Mage::helper('xsitemap')->__('New Sitemap');
        }
    }

}
