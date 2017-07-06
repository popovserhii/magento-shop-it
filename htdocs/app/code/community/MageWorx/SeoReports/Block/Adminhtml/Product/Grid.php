<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoReports_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $store = $this->_getStore();
        $maxLengthMetaTitle = Mage::helper('seoreports/config')->getMaxLengthMetaTitle();
        $maxLengthMetaDescription = Mage::helper('seoreports/config')->getMaxLengthMetaDescription();

        $collection = Mage::getResourceModel('seoreports/product_collection');
        if ($store) {
            $collection->addFieldToFilter('store_id', $store);

            $additionalCondition = $this->_getAdditionalCondition();

            $collection->getSelect()->where(
                "
                `meta_title_len` = 0   OR
                `meta_title_len` > " . $maxLengthMetaTitle . "  OR
                `meta_descr_len` = 0   OR
                `meta_descr_len` > " . $maxLengthMetaDescription . " OR
                `name_dupl` > 1        OR
                `meta_title_dupl` > 1 OR
                `prepared_meta_title` = '' " .
                $additionalCondition
            );

        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addFieldToFilter('store_id', $value);
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('seoreports')->__('ID'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'entity_id',
            'align'  => 'center',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('seoreports')->__('Product Name'),
            'type'   => 'text',
            'index'  => 'name',
            'align'  => 'left',
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('seoreports')->__('SKU'),
            'type'   => 'text',
            'index'  => 'sku'
        ));


        $this->addColumn('url', array(
            'header'   => Mage::helper('seoreports')->__('Url'),
            'renderer' => 'seoreports/adminhtml_grid_renderer_url',
            'type'     => 'text',
            'index'    => 'url_path',
            'align'    => 'left',
        ));

        $this->addColumn('type', array(
            'header'  => Mage::helper('seoreports')->__('Type'),
            'width'   => '125px',
            'index'   => 'type_id',
            'type'    => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            'align'   => 'center'
        ));

        $this->addColumn('name_error', array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(array('duplicate')),
            'header'   => Mage::helper('seoreports')->__('Name'),
            'index'    => 'name_error',
            'width'    => '100px',
            'align'    => 'center',
            'sortable' => false,
        ));

        $this->addColumn('meta_title_error', array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(),
            'header'   => Mage::helper('seoreports')->__('Meta Title'),
            'index'    => 'meta_title_error',
            'width'    => '100px',
            'align'    => 'center',
            'sortable' => false,
        ));

        $this->addColumn('meta_descr_error', array(
            'renderer' => 'seoreports/adminhtml_grid_renderer_error',
            'filter'   => 'seoreports/adminhtml_grid_filter_error',
            'type'     => 'options',
            'options'  => Mage::helper('seoreports')->getErrorTypes(array('missing', 'long')),
            'header'   => Mage::helper('seoreports')->__('Meta Description'),
            'index'    => 'meta_descr_error',
            'width'    => '100px',
            'align'    => 'center',
            'sortable' => false,
        ));

        if (!Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            $this->addColumn('url_error', array(
                'renderer' => 'seoreports/adminhtml_grid_renderer_error',
                'filter'   => 'seoreports/adminhtml_grid_filter_error',
                'type'     => 'options',
                'options'  => Mage::helper('seoreports')->getErrorTypes(array('duplicate')),
                'header'   => Mage::helper('seoreports')->__('URL key duplicate'),
                'index'    => 'url_error',
                'width'    => '100px',
                'align'    => 'center',
                'sortable' => false,
            ));
        }

        $this->addColumn('action', array(
            'header'    => Mage::helper('seoreports')->__('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'    => 'getProductId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('seoreports')->__('Edit'),
                    'url'     => array(
                                    'base'   => 'adminhtml/catalog_product/edit/',
                                    'params' => array('store' => $this->getRequest()->getParam('store'))
                                ),
                    'field'   => 'id'
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

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit',
            array(
                'id'    => $row->getProductId(),
                'store' => $this->getRequest()->getParam('store'))
            );
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _getAdditionalCondition()
    {
        if (!Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            return " OR `url_dupl` > 1";
        }
    }

}
