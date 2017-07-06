<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Block_Adminhtml_Crosslink_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('seocrosslinks_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir(Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setSaveParametersInSession(true);
    }


    /**
     * Set collection of grid
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('mageworx_seocrosslinks/crosslink_collection');
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
        $helperData = Mage::helper('mageworx_seocrosslinks');

        $this->addColumn(
            'crosslink_id',
            array(
                'header' => Mage::helper('catalog')->__('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'crosslink_id'
            )
        );

        $this->addColumn(
            'keyword',
            array(
                'header' => Mage::helper('mageworx_seocrosslinks')->__('Keyword'),
                'align'  => 'right',
                'index'  => 'keyword'
            )
        );

        if ($helperData->showLinkTitleColumn()) {
            $this->addColumn(
                'link_title',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Link Title'),
                    'align'  => 'right',
                    'index'  => 'link_title'
                )
            );
        }

        if ($helperData->showLinkTargetColumn()) {
            $this->addColumn(
                'link_target',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Link Target'),
                    'align'  => 'right',
                    'index'  => 'link_target',
                    'type'    => 'options',
                    'options' => Mage::getSingleton('mageworx_seocrosslinks/crosslink')->getTargetLinkArray()
                )
            );
        }

        if ($helperData->showStoreViewColumn() && !Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('catalog')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        if ($helperData->showStaticUrlColumn()) {
            $this->addColumn(
                'ref_static_url',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Custom URL'),
                    'align'  => 'right',
                    'index'  => 'ref_static_url'
                )
            );
        }

        if ($helperData->showProductBySkuColumn()) {
            $this->addColumn(
                'ref_product_sku',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Product SKU'),
                    'align'  => 'right',
                    'index'  => 'ref_product_sku'
                )
            );
        }

        if ($helperData->showCategoryByIdColumn()) {
            $this->addColumn(
                'ref_category_id',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Category ID'),
                    'align'  => 'right',
                    'index'  => 'ref_category_id'
                )
            );
        }

        if ($helperData->showReplacementCountColumn()) {
            $this->addColumn(
                'replacement_count',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Replacement Count'),
                    'align'  => 'right',
                    'index'  => 'replacement_count'
                )
            );
        }

        $this->addColumn(
            'priority',
            array(
                'header' => Mage::helper('mageworx_seocrosslinks')->__('Priority'),
                'align'  => 'right',
                'index'  => 'priority',
            )
        );

        if ($helperData->showInProductColumn()) {
            $this->addColumn(
                'in_product',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Use in Product Page'),
                    'align'  => 'right',
                    'index'  => 'in_product',
                    'type'    => 'options',
                    'options' => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            );
        }

        if ($helperData->showInCategoryColumn()) {
            $this->addColumn(
                'in_category',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Use in Category Page'),
                    'align'  => 'right',
                    'index'  => 'in_category',
                    'type'    => 'options',
                    'options' => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            );
        }

        if ($helperData->showInCmsPageColumn()) {
            $this->addColumn(
                'in_cms_page',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Use in CMS Page'),
                    'align'  => 'right',
                    'index'  => 'in_cms_page',
                    'type'    => 'options',
                    'options' => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            );
        }

        if ($helperData->showInBlogColumn()) {
            $this->addColumn(
                'in_blog',
                array(
                    'header' => Mage::helper('mageworx_seocrosslinks')->__('Use in Blog Post Page'),
                    'align'  => 'right',
                    'index'  => 'in_blog',
                    'type'    => 'options',
                    'options' => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            );
        }

        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('catalog')->__('Enabled'),
                'align'   => 'right',
                'width'   => '75px',
                'index'   => 'status',
                'type'    => 'options',
                'options' => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
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
        $this->setMassactionIdField('crosslink_id');
        $this->getMassactionBlock()->setFormFieldName('crosslinks');

        $this->getMassactionBlock()->addItem('link_target',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__('Change Link Target'),
            'url'        => $this->getUrl('*/*/massChangeLinkTarget', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'link_target',
                    'type'     => 'select',
                    'label'    => Mage::helper('mageworx_seocrosslinks')->__('Link Target'),
                    'values'   => Mage::getSingleton('mageworx_seocrosslinks/crosslink')->getTargetLinkArray()
                )
            )
        ));

        $this->getMassactionBlock()->addItem('replacement_count',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__('Change Replacement Count'),
            'url'        => $this->getUrl('*/*/massChangeReplacementCount', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'replacement_count',
                    'type'     => 'text',
                    'class'    => 'required-entry validate-not-negative-number',
                    'label'    => Mage::helper('mageworx_seocrosslinks')->__('Replacement Count'),
                )
            )
        ));

        $this->getMassactionBlock()->addItem('priority',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__("Change Priority"),
            'url'        => $this->getUrl('*/*/massChangePriority', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'  => 'priority',
                    'type'  => 'text',
                    'class' => 'required-entry validate-not-negative-number',
                    'label' => Mage::helper('mageworx_seocrosslinks')->__('Priority'),
                )
            )
        ));

         $this->getMassactionBlock()->addItem('in_product',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__("Change 'Use in Product Page'"),
            'url'        => $this->getUrl('*/*/massChangeUseInProduct', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'in_product',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('mageworx_seocrosslinks')->__('Use in Product Page'),
                    'values'   => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            )
        ));

        $this->getMassactionBlock()->addItem('in_category',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__("Change 'Use in Category Page'"),
            'url'        => $this->getUrl('*/*/massChangeUseInCategory', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'in_category',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('mageworx_seocrosslinks')->__('Use in Category Page'),
                    'values'   => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            )
        ));

        $this->getMassactionBlock()->addItem('in_cms_page',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__("Change 'Use in CMS Page'"),
            'url'        => $this->getUrl('*/*/massChangeUseInCmsPage', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'in_cms_page',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('mageworx_seocrosslinks')->__('Use in CMS Page'),
                    'values'   => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            )
        ));

        $this->getMassactionBlock()->addItem('in_blog',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__("Change 'Use in Blog Post Page'"),
            'url'        => $this->getUrl('*/*/massChangeUseInBlog', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'in_blog',
                    'type'    => 'select',
                    'class'   => 'required-entry',
                    'label'   => Mage::helper('mageworx_seocrosslinks')->__('Use in Blog Post Page'),
                    'values'   => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            )
        ));

        $this->getMassactionBlock()->addItem('status',
            array(
            'label'      => Mage::helper('mageworx_seocrosslinks')->__("Change 'Enabled/Disabled'"),
            'url'        => $this->getUrl('*/*/massChangeStatus', array('_current' => true, 'store' => $this->getStoreId())),
            'additional' => array(
                'visibility' => array(
                    'name'     => 'status',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('mageworx_seocrosslinks')->__('Enabled'),
                    'values'   => Mage::getSingleton('mageworx_seocrosslinks/source_yesno')->toArray()
                )
            )
        ));

        $this->getMassactionBlock()->addItem('delete',
            array(
            'label'   => Mage::helper('catalog')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('mageworx_seocrosslinks')->__('Are you sure you want to do this?')
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
        return $this->getUrl('*/*/edit', array('crosslink_id' => $row->getId()));
    }

    /**
     * Add store data for each model in collection
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     *
     * @param MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return void
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }
}