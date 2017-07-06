<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('templateGrid');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_ASC);
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('template_id' => $row->getTemplateId(), 'store' => $row->getStoreId()));
    }

    public function getStoreId()
    {
        $store = $this->getRequest()->getParam('store');
        if (!$store) {
            $store = 0;
        }
        return $store;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * Prepare template grid collection object
     *
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    protected function _prepareCollection()
    {
        $itemType = Mage::helper('mageworx_seoxtemplates/factory')->getItemType();
        $collection = Mage::getResourceModel("mageworx_seoxtemplates/template_{$itemType}_collection");
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare template grid columns
     *
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    protected function _prepareColumns()
    {
        $this->addColumn('template_id',
            array(
            'header'       => Mage::helper('catalog')->__('ID'),
            'index'        => 'template_id',
            'type'         => 'number',
            'width'        => 50,
        ));

        $this->addColumn('name',
            array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index'  => 'name',
        ));

        $this->addColumn('type',
            array(
            'header'  => Mage::helper('catalog')->__('Type'),
            'width'   => 200,
            'index'   => 'type_id',
            'type'    => 'options',
            'options' => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getTypeArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header' => Mage::helper('catalog')->__('Store'),
                'index' => 'store_id',
                'type' => 'store',
                'store_all' => true,
                'filter' => 'mageworx_seoxtemplates/adminhtml_widget_grid_column_filter_store',
                'store_view' => true,
                'sortable' => false,
                'skipEmptyStoresLabel' => true,
            ));
        }

        $this->addColumn('assign_type',
            array(
            'header'  => Mage::helper('mageworx_seoxtemplates')->__('Assigned'),
            'width'   => 200,
            'index'   => 'assign_type',
            'type'    => 'options',
            'options' => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getAssignTypeArray(),
        ));

        $this->addColumn('priority',
            array(
            'header' => Mage::helper('mageworx_seoxtemplates')->__('Priority'),
            'width'  => 100,
            'index'  => 'priority',
            'type'   => 'number'
        ));

        $this->addColumn('date_modified',
            array(
            'header'   => Mage::helper('mageworx_seoxtemplates')->__('Last modified'),
            'index'    => 'date_modified',
            'type'     => 'datetime',
            'align'    => 'center',
            'default'  => '---',
            'filter'   => false,
            'sortable' => true,
        ));

        $this->addColumn('date_apply_start',
            array(
            'header'   => Mage::helper('mageworx_seoxtemplates')->__('Last start'),
            'index'    => 'date_apply_start',
            'type'     => 'datetime',
            'align'    => 'center',
            'filter'   => false,
            'sortable' => true,
        ));

        $this->addColumn('date_apply_finish',
            array(
            'header'   => Mage::helper('mageworx_seoxtemplates')->__('Last finish'),
            'index'    => 'date_apply_finish',
            'type'     => 'datetime',
            'align'    => 'center',
            'filter'   => false,
            'sortable' => true,
        ));

        $this->addColumn('write_for',
            array(
            'header'  =>  Mage::helper('mageworx_seoxtemplates')->__('Apply For'),
            'width'   => '80px',
            'index'   => 'write_for',
            'type'    => 'options',
            'options' => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getWriteForArray(),
            'align'   => 'center'
        ));

        $this->addColumn('action_apply_test',
            array(
            'header'    => Mage::helper('customer')->__('Apply Template'),
            'type'      => 'action',
            'getter'    => 'getId',
            'width'     => '50px',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('customer')->__('Test Apply'),
                    'url'     => array('base'   => '*/*/apply',
                    'params' => array('test' => 'csv', 'store' => $this->getStoreId())),
                    'field'   => 'template_id'
                ),
            ),
            'width'     => 30,
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true,
            'align'   => 'center'
        ));

        $confirmMessage = $this->__('This action cannot be canceled. Are you sure you want to continue?');
        $this->addColumn('action_apply',
            array(
            'header'    => Mage::helper('customer')->__('Apply Template'),
            'type'      => 'action',
            'getter'    => 'getId',
            'width'     => '50px',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('customer')->__('Apply'),
                    'onclick' => "if(confirm('{$confirmMessage}')) { return true; } else { return false; }",
                    'url'     => array('base'   => '*/*/apply',
                    'params' => array('store' => $this->getStoreId())),
                    'field'   => 'template_id'
                ),
            ),
            'width'     => 30,
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true,
            'align'   => 'center'
        ));

        $this->addColumn('use_cron',
            array(
            'header'  => Mage::helper('mageworx_seoxtemplates')->__('Apply By Cron'),
            'width'   => '80px',
            'index'   => 'use_cron',
            'type'    => 'options',
            'options' => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getUseCronArray(),
            'align'   => 'center'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('templates');

        $this->getMassactionBlock()->addItem('Apply',
            array(
            'label'   => Mage::helper('mageworx_seoxtemplates')->__('Apply'),
            'url'     => $this->getUrl('*/*/massApply'),
            'confirm' => Mage::helper('mageworx_seoxtemplates')->__('This action cannot be canceled. Are you sure you want to continue?')
        ));

        $writeForArray = Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getWriteForArray();
        array_unshift($writeForArray, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('write_for',
            array(
            'label'      => Mage::helper('mageworx_seoxtemplates')->__('Change "Apply for"'),
            'url'        => $this->getUrl('*/*/massApplyFor', array('_current' => true, 'store'    => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'write_for',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('mageworx_seoxtemplates')->__('Apply for'),
                    'values' => $writeForArray
                )
            )
        ));

        $useCroneArray = array_reverse(Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getUseCronArray());
        array_unshift($useCroneArray, array('label' => '', 'value' => ''));

        $this->getMassactionBlock()->addItem('use_cron',
            array(
            'label'      => Mage::helper('mageworx_seoxtemplates')->__('Change "Apply By Cron"'),
            'url'        => $this->getUrl('*/*/massCron', array('_current' => true, 'store'    => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'   => 'use_cron',
                    'type'   => 'select',
                    'class'  => 'required-entry',
                    'label'  => Mage::helper('mageworx_seoxtemplates')->__('Use Cron'),
                    'values' => $useCroneArray
                )
            )
        ));

        $this->getMassactionBlock()->addItem('delete',
            array(
            'label'   => Mage::helper('catalog')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('mageworx_seoxtemplates')->__('Are you sure you want to do this?')
        ));

        return $this;
    }
}
