<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_id');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
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

        $collection = Mage::getResourceModel('seoreports/category_collection');
        if ($store) {
            $collection->addFieldToFilter('store_id', $store);
        }
        else {
            $collection->getSelect()->where('store_id <>?', 0);
        }

        $collection->getSelect()->where('store_id <>?', 0);

        $collection->getSelect()->where(
            "`meta_title_len` = 0   OR
            `meta_title_len` > " . $maxLengthMetaTitle . "  OR
            `meta_descr_len` = 0   OR
            `meta_descr_len` > " . $maxLengthMetaDescription . " OR
            `name_dupl` > 1        OR
            `meta_title_dupl` > 1 OR
            `prepared_meta_title` = ''"
        );

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
            'index'  => 'entity_id',
            'align'  => 'center',
        ));

        $this->addColumn('name',
                array(
            'header' => Mage::helper('seoreports')->__('Category Name'),
            'type'   => 'text',
            'index'  => 'name',
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

        $this->addColumn('level',
                array(
            'header' => Mage::helper('seoreports')->__('Level'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'level',
            'align'  => 'center',
        ));

        $this->addColumn('name_error',
                array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(array('duplicate')),
            'header'   => Mage::helper('seoreports')->__('Name'),
            'index'    => 'name_error',
            'width'    => '150px',
            'align'    => 'center',
            'sortable' => false,
        ));

        $this->addColumn('meta_title_error',
                array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(),
            'header'   => Mage::helper('seoreports')->__('Meta Title'),
            'index'    => 'meta_title_error',
            'width'    => '150px',
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
            'width'    => '150px',
            'sortable' => false,
            'align'    => 'center',
        ));
        $this->addColumn('store',
                array(
            'header'                    => Mage::helper('seoreports')->__('Store View'),
            'index'                     => 'store_id',
            'type'                      => 'store',
            'store_all'                 => true,
            'store_view'                => true,
            'sortable'                  => false,
            'filter_condition_callback'
            => array($this, '_filterStoreCondition'),
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
        return $this->getUrl('adminhtml/catalog_category/edit',
                        array('id'    => $row->getCategoryId(), 'store' => $this->getRequest()->getParam('store')));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
