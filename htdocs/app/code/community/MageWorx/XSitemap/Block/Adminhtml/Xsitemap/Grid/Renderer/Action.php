<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Block_Adminhtml_Xsitemap_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $this->getColumn()->setActions(array(array(
                'url'     => $this->getUrl('*/mageworx_xsitemap/generate', array('sitemap_id' => $row->getSitemapId())),
                'caption' => Mage::helper('xsitemap')->__('Generate'),
        )));
        return parent::render($row);
    }

}
