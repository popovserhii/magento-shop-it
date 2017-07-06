<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Blog_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('templateProductGrid');
        $this->setDefaultSort('main_table.post_id');
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

        if ($this->_getSelectedProducts()) {
            $this->setDefaultFilter(array('massaction' => 1));
        }
    }

    /**
     * Add filter to collection
     *
     * @param mixed $column
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Product_Edit_Tab_Conditions
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'productids') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.post_id', array('in' => $productIds));
            }
            else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('main_table.post_id', array('nin' => $productIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Set filtered product collection
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Product_Edit_Tab_Conditions
     */
    protected function _prepareCollection()
    {
        $excludeBlogIds = Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getAssignForAnalogTemplateBlogIds();
        $collection = Mage::getResourceModel('mageworx_seoxtemplates/blog_collection')
            ->setFlag('mageworx_store', 1)
            ->joinStores()
            ->joinCategories();

        if (!empty($excludeBlogIds)) {
            $collection->getSelect()->where('main_table.post_id NOT IN (?)', $excludeBlogIds);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title',
            array(
            'header' => Mage::helper('catalog')->__('Title'),
            'index'  => 'title',
            'filter_index' => 'main_table.title',
        ));

        $this->addColumn('identifier',
            array(
            'header' => Mage::helper('catalog')->__('Identifier'),
            'width'  => 100,
            'index'  => 'identifier',
            'filter_index' => 'main_table.identifier',
        ));

        $this->addColumn('meta_description',
            array(
            'header' => Mage::helper('catalog')->__('Meta Description'),
            'index'  => 'title',
            'filter_index' => 'main_table.meta_description',
        ));

        $this->addColumn('meta_keywords',
            array(
            'header' => Mage::helper('catalog')->__('Meta Keywords'),
            'index'  => 'title',
            'filter_index' => 'main_table.meta_keywords',
        ));

        $this->addColumn('category',
            array(
            'header'                    => Mage::helper('catalog')->__('Category'),
            'index'                     => 'cat_ids',
            'type'                      => 'options',
            'validate_class'            => 'validate-number',
            'options'                   => Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->getBlogCategoryOptionArray(),
            'renderer'                  => 'MageWorx_SeoXTemplates_Block_Adminhtml_Widget_Grid_Column_Renderer_Postcat',
            'filter_condition_callback' => array($this, 'categoryFilter')
        ));

        $this->addColumn('user',
            array(
            'header' => Mage::helper('catalog')->__('Author'),
            'width'  => 100,
            'index'  => 'user',
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_ids', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_ids',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('status',
            array(
            'header'  => Mage::helper('catalog')->__('Status'),
            'width'   => 70,
            'index'   => 'status',
            'type'    => 'options',
            'options' => Mage::getSingleton('blog/status')->getOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Add switcher template to massaction block
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Product_Edit_Tab_Conditions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('main_table.post_id');
        $massBlock = $this->getMassactionBlock();
        $massBlock->setTemplate('mageworx/seoxtemplates/widget-grid-massaction.phtml');

        $massBlock->setFormFieldName('in_products');
        $massBlock->addItem(null, array());

        $productIds = $this->_getSelectedProducts();

        if ($productIds) {
            $productIdsAsString = implode(',', $productIds);
            $massBlock->getRequest()->setPost($massBlock->getFormFieldNameInternal(), $productIdsAsString);
        }

        return $this;
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * Get product grid URL
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/templateGrid',
            array(
                '_current' => true,
                //'store'    => Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getStoreId(),
                'type_id'  => Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getTypeId(),
            )
        );
    }

    /**
     * Retrive product ids from template model
     * @return array|null
     */
    protected function _getSelectedProducts()
    {
        $template = Mage::helper('mageworx_seoxtemplates/factory')->getModel();

        if(Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForIndividualItems($template->getAssignType())){
            return $template->getInItems();
        }
        return null;
    }

    public function categoryFilter($collection, $column)
    {
        $cond = $column->getFilter()->getCondition();
        if (empty($cond['eq'])) {
            return true;
        }
        $where = 'cat_table.cat_id = ' . $cond['eq'];
        $collection->getSelect()->where($where);
    }

    /**
     * Add switcher All Products / Attribute Set Products / Individual Product(s)
     * If isset template for current store(s) which assigned for all products
     * "All Products" switcher will be disabled.
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getRequest()->getParam('isAjax')) {
            return parent::_toHtml();
        }

        if (Mage::helper('mageworx_seoxtemplates')->getStep() == 'new_step_2' || Mage::helper('mageworx_seoxtemplates')->getStep() ==
            'edit') {
            $duplicate  = Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getAllTypeDuplicateTemplate();
            $attributes = $this->_getHtmlAttributes($duplicate);
        }

        $switcher = '
            <div class="switcher">
                <span class="switcher_title">' . Mage::helper('mageworx_seoxtemplates')->__('Assign by:') . '</span>
                <label><input type="radio"' . $attributes['disabled'] . ' ' . $attributes['title'] . ' name="general[assign_type]" value="1"' . $attributes['checkedAll'] . ' onclick="assignBy(this.value)" /> '
            . Mage::helper('mageworx_seoxtemplates')->__('All Blog Posts') .
            '</label>
                <label><input type="radio" name="general[assign_type]" value="2"' . $attributes['checkedIndividual'] . ' onclick="assignBy(this.value)" /> '
            . Mage::helper('mageworx_seoxtemplates')->__('Individual Blog Post(s)') .
            '</label>
            </div>
            <script type="text/javascript">
            //<![CDATA[
                function assignBy(type) {
                    if (type == 2) {
                        $("templateProductGrid").show();
                    } else {
                        $("templateProductGrid").hide();
                    }
                }' . $attributes['hideIndividualJS'] . '
            //]]>
            </script>
        ';
        return $switcher . parent::_toHtml();
    }

    /**
     * Retrive HTML attributes for switcher
     * @param MageWorx_SeoXTemplates_Model_Mysql4_Template_Product_Collection $duplicate|null
     * @return array
     */
    protected function _getHtmlAttributes($duplicate)
    {
        $data = array();

        $template = Mage::helper('mageworx_seoxtemplates/factory')->getModel();

        if (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForAllItems($template->getAssignType())) {

            $data['checkedAll']        = ' checked';
            $data['checkedIndividual'] = '';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('templateProductGrid'));
        }
        elseif (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForIndividualItems($template->getAssignType())) {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = ' checked';
            $data['hideIndividualJS']  = '';
        }
        elseif (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForGroupItems($template->getAssignType())) {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = '';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('templateProductGrid'));
        }
        else {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = ' checked';
            $data['hideIndividualJS']  = '';
        }

        if ($duplicate) {
            $data['disabled'] = ' disabled';
            $data['title']    = ' title="' . Mage::helper('mageworx_seoxtemplates')->__('You have already created a similar template (%s), which assigned for all product/categories of the templates store(s)',
                    $duplicate->getName()) . '"';
        }
        else {
            $data['disabled'] = '';
            $data['title']    = '';
        }
        return $data;
    }
}
