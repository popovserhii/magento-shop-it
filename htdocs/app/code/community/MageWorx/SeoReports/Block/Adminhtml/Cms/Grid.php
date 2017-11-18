<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Block_Adminhtml_Cms_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

        if (!Mage::registry('error_types')){
            Mage::register('error_types', Mage::helper('seoreports')->getErrorTypes());
        }
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return $storeId ? $storeId : Mage::app()->getStore(true)->getId();
    }

    protected function _prepareCollection()
    {
        $store      = $this->_getStore();
        $maxLengthMetaTitle = Mage::helper('seoreports/config')->getMaxLengthMetaTitle();
        $maxLengthMetaDescription = Mage::helper('seoreports/config')->getMaxLengthMetaDescription();

        $collection = Mage::getResourceModel('seoreports/cms_collection');
        $collection->addFieldToFilter('store_id', $store);

         $collection->getSelect()->where("`meta_title_len` = 0   OR
                                        `meta_title_len` > " . $maxLengthMetaTitle . "  OR
                                        `meta_descr_len` = 0   OR
                                        `meta_descr_len` > " . $maxLengthMetaDescription . " OR
                                        `heading_dupl` > 1        OR
                                        `meta_title_dupl` > 1 OR
                                        `prepared_heading` = '' OR
                                        `prepared_meta_title` = ''
                                        " );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id',
                array(
            'header' => Mage::helper('seoreports')->__('ID'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'entity_id',
            'align'  => 'center',
        ));

        $this->addColumn('meta_title',
                array(
            'header' => Mage::helper('seoreports')->__('Title'),
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

        $this->addColumn('heading_error',
                array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(array('missing', 'duplicate')),
            'header'   => Mage::helper('seoreports')->__('Content Heading'),
            'index'    => 'heading_error',
            'sortable' => false,
            'width'    => '100px',
            'align'    => 'center',
        ));

        $this->addColumn('meta_title_error',
                array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(array('long', 'duplicate')),
            'header'   => Mage::helper('seoreports')->__('Meta Title'),
            'index'    => 'meta_title_error',
            'width'    => '100px',
            'sortable' => false,
            'align'    => 'center',
        ));

        $this->addColumn('meta_descr_error',
                array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(array('missing', 'long')),
            'header'   => Mage::helper('seoreports')->__('Meta Description'),
            'index'    => 'meta_descr_error',
            'width'    => '100px',
            'sortable' => false,
            'align'    => 'center',
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

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addFieldToFilter('store_id', $value);
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
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
