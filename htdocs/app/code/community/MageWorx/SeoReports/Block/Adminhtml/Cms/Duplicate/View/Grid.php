<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Block_Adminhtml_Cms_Duplicate_View_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('page_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASK');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', Mage::app()->getDefaultStoreView()->getStoreId());
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store      = $this->_getStore();
        $collection = Mage::getResourceModel('seoreports/cms_collection')->addFieldToFilter('store_id',
                $store->getId());

        $preparedHeading = $this->getRequest()->getParam('prepared_heading', '');
        if ($preparedHeading) $collection->addFieldToFilter('prepared_heading', $preparedHeading);

        $preparedMetaTitle = $this->getRequest()->getParam('prepared_meta_title', '');
        if ($preparedMetaTitle) $collection->addFieldToFilter('prepared_meta_title', $preparedMetaTitle);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('id',
                array(
            'header' => Mage::helper('seoreports')->__('ID'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'page_id',
            'align'  => 'center',
        ));


        $this->addColumn('heading',
                array(
            'header' => Mage::helper('seoreports')->__('Content Heading'),
            'type'   => 'text',
            'index'  => 'heading',
            'align'  => 'left',
        ));


        $this->addColumn('meta_title',
                array(
            'header' => Mage::helper('seoreports')->__('Meta Title'),
            'type'   => 'text',
            'index'  => 'meta_title',
            'align'  => 'left',
        ));


        $this->addColumn('url',
                array(
            'header'   => Mage::helper('seoreports')->__('Url'),
            'renderer' => 'seoreports/adminhtml_grid_renderer_url',
            'type'     => 'text',
            'index'    => 'url_path',
            'align'    => 'left',
        ));


        $this->addColumn('action',
                array(
            'header'    => Mage::helper('seoreports')->__('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'    => 'getPageId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('seoreports')->__('Edit'),
                    'url'     => array('base' => 'adminhtml/cms_page/edit/'),
                    'field'   => 'page_id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'align'     => 'center',
            'is_system' => true,
        ));


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/cms_page/edit', array('page_id' => $row->getPageId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/duplicateViewGrid', array('_current' => true));
    }

}
