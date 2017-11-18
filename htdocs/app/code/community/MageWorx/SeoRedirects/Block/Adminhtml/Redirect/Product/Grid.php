<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('seoredirects_product_grid');
        $this->setDefaultSort('redirect_id');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setSaveParametersInSession(true);
    }

    /**
     *
     * @param type $item
     * @return string
     */
    public function getRowClass($item)
    {
        $requestSort = $this->getParam($this->getVarNameSort());

        if ($requestSort && !in_array($requestSort, $this->_getAvailableBorderSorts())) {
            return '';
        }

        $borderRedirectIds = $this->_getBorderRedirectIds($item);

        if (in_array($item->getRedirectId(), $borderRedirectIds)) {
            return 'mageworx-redirect-border';
        }

        return '';
    }

    /**
     *
     * @return array
     */
    protected function _getAvailableBorderSorts()
    {
        return array($this->_defaultSort, 'redirect_id', 'product_id', 'product_sku');
    }

    /**
     *
     * @return array
     */
    protected function _getBorderRedirectIds()
    {
        if ($this->_borderRedirectIds == null) {
            $borderRedirectIds = array();
            $redirects = $this->getCollection()->getItems();

            $lastProductId  = null;
            $lastRedirectId = null;

            foreach ($redirects as $redirect) {

                if (!empty($lastProductId) && $lastProductId != $redirect->getProductId()) {
                    $borderRedirectIds[] = $lastRedirectId;
                }

                $lastRedirectId = $redirect->getRedirectId();
                $lastProductId  = $redirect->getProductId();
            }

            $this->_borderRedirectIds = $borderRedirectIds;
        }

        return $this->_borderRedirectIds === null ? array() : $this->_borderRedirectIds;
    }

    /**
     * Set collection of grid
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageworx_seoredirects/redirect_product_collection');
        $collection->addStoreFilter($this->_getStoreId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return this
     */
    protected function _prepareColumns()
    {
        $helperData = Mage::helper('mageworx_seoredirects');

        $this->addColumn(
            'redirect_id',
            array(
                'header' => Mage::helper('catalog')->__('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'redirect_id',
            )
        );

        $this->addColumn(
            'product_id',
            array(
                'header' => Mage::helper('catalog')->__('Product ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'product_id',
                'type'   => 'number',
            )
        );

        $this->addColumn(
            'product_sku',
            array(
                'header' => $helperData->__('Product SKU'),
                'align'  => 'right',
                'index'  => 'product_sku'
            )
        );

        $this->addColumn(
            'product_name',
            array(
                'header' => Mage::helper('catalog')->__('Product Name'),
                'align'  => 'left',
                'index'  => 'product_name'
            )
        );

        $this->addColumn(
            'request_path',
            array(
                'header' => Mage::helper('adminhtml')->__('Request Path'),
                'align'  => 'left',
                'index'  => 'request_path'
            )
        );

        $this->addColumn(
            'category_id',
            array(
                'header' => $helperData->__('Product Category ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'category_id',
                'type'   => 'number',
            )
        );

        $this->addColumn('category_name',
            array(
                'header'	=> $helperData->__('Product Category Name'),
                'index'		=> 'category_id',
                'sortable'	=> false,
                'width' => '250px',
                'type'  => 'options',
                'options'	=> Mage::getSingleton('mageworx_seoredirects/source_category')->toOptionArray(),
                'renderer'	=> 'mageworx_seoredirects/adminhtml_redirect_product_render_category',
                'filter_condition_callback' => array($this, 'filterCallback'),
            ),'name');

        $this->addColumn(
            'priority_category_id',
            array(
                'header' => $helperData->__('Priority Category ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'priority_category_id',
                'type'   => 'number',
            )
        );

        $this->addColumn('priority_category_name',
            array(
                'header'	=> $helperData->__('Priority Category Name'),
                'index'		=> 'priority_category_id',
                'sortable'	=> false,
                'width' => '250px',
                'type'  => 'options',
                'options'	=> Mage::getSingleton('mageworx_seoredirects/source_category')->toOptionArray(),
                'renderer'	=> 'mageworx_seoredirects/adminhtml_redirect_product_render_category_priority',
                'filter_condition_callback' => array($this, 'filterCallback'),
            ),'name');


        $this->addColumn('hits',
            array(
                'header' => $helperData->__('Hits'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'hits',
        ));

        $this->addColumn('date_created',
            array(
            'header'   => Mage::helper('cms')->__('Date Created'),
            'index'    => 'date_created',
            'type'     => 'datetime',
            'align'    => 'center',
            'default'  => '---',
            'sortable' => true,
        ));

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('catalog')->__('Enabled'),
                'align'   => 'right',
                'width'   => '75px',
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getSingleton('mageworx_seoredirects/source_yesno')->toArray()
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @return this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('redirect_id');
        $this->getMassactionBlock()->setFormFieldName('redirects');

        $this->getMassactionBlock()->addItem('status',
            array(
                'label' => Mage::helper('mageworx_seoredirects')->__('Change "Enabled/Disabled"'),
                'url'   => $this->getUrl('*/*/massChangeStatus', array('_current' => true, 'store' => $this->_getStoreId())),
                'additional' => array(
                    'visibility' => array(
                        'name'     => 'status',
                        'type'     => 'select',
                        'class'    => 'required-entry',
                        'label'    => Mage::helper('cms')->__('Enabled'),
                        'values'   => Mage::getSingleton('mageworx_seoredirects/source_yesno')->toArray()
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem('category_id',
            array(
                'label' => Mage::helper('mageworx_seoredirects')->__('Change Product Category'),
                'url'   => $this->getUrl('*/*/massChangeCategory', array('_current' => true, 'store' => $this->_getStoreId())),
                'additional' => array(
                    'visibility' => array(
                        'name'     => 'category_id',
                        'type'     => 'select',
                        'class'    => 'required-entry',
                        'label'    => Mage::helper('mageworx_seoredirects')->__('Choose:'),
                        'values'   => Mage::getSingleton('mageworx_seoredirects/source_category')->toOptionArray()
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem('priority_category_id',
            array(
                'label' => Mage::helper('mageworx_seoredirects')->__('Change Priority Category'),
                'url'   => $this->getUrl('*/*/massChangePriorityCategory', array('_current' => true, 'store' => $this->_getStoreId())),
                'additional' => array(
                    'visibility' => array(
                        'name'     => 'priority_category_id',
                        'type'     => 'select',
                        'class'    => 'required-entry',
                        'label'    => Mage::helper('mageworx_seoredirects')->__('Choose:'),
                        'values'   => Mage::getSingleton('mageworx_seoredirects/source_category')->toOptionArray()
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem('hits',
            array(
                'label' => Mage::helper('mageworx_seoredirects')->__('Change Hits'),
                'url'   => $this->getUrl('*/*/massChangeHits', array('_current' => true, 'store' => $this->_getStoreId())),
                'additional' => array(
                    'visibility' => array(
                        'name'     => 'hits',
                        'type'     => 'text',
                        'label'    => Mage::helper('mageworx_seoredirects')->__('Count'),
                        'class'    => 'required-entry validate-not-negative-number',
                    )
                )
            )
        );

        $this->getMassactionBlock()->addItem('delete',
            array(
            'label'   => Mage::helper('catalog')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete', array('_current' => true, 'store' => $this->_getStoreId())),
            'confirm' => Mage::helper('mageworx_seoredirects')->__('Are you sure you want to do this?')
        ));

        return $this;
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('redirect_id' => $row->getId(), 'store' => $this->_getStoreId()));
    }

    /**
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)Mage::app()->getRequest()->getParam('store');
    }

    /**
     *
     * @param MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection $collection
     * @param type $column
     * @return MageWorx_SeoRedirects_Model_Resource_Redirect_Product_Collection
     */
    public function filterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $_category = Mage::getModel('catalog/category')->load($value);
        $collection->addCategoryFilter($_category->getId());

        return $collection;
    }
}