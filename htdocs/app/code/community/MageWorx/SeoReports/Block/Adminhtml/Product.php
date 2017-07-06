<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Container
{

    protected function _prepareLayout()
    {
        $this->_addButton('generate',
                array(
            'label'   => Mage::helper('seoreports')->__('Generate'),
            'onclick' => "setLocation('{$this->getUrl('*/*/generate')}')",
            'class'   => 'generate'
        ));
        $storeId = (int) $this->getRequest()->getParam('store');
        if (!$storeId) {
            Mage::app()->getRequest()->setParam('store', Mage::app()->getStore(true)->getId());
        }
        $this->setChild('grid',
                $this->getLayout()->createBlock('seoreports/adminhtml_product_grid', 'seoreports.product.grid'));
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}
