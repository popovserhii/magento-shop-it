<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Product_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('templateProductGrid');
        $this->setDefaultSort('entity_id');
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
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            }
            else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
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
        $excludeProductIds = Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getAssignForAnalogTemplateProductIds();

        /**
         * @todo store request check
         */
        $store      = $this->_getStore();
        $collection = Mage::getResourceModel('mageworx_seoxtemplates/catalog_product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1',
            'left');

        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner',
                $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $collection->getSelect()
            ->joinLeft(array('category' => $collection->getTable('catalog/category_product')),
                'e.entity_id = category.product_id', array('cat_ids' => 'GROUP_CONCAT(category.category_id)'))
            ->group('e.entity_id');

        if (!empty($excludeProductIds)) {
            $collection->getSelect()->where('e.entity_id NOT IN (?)', $excludeProductIds);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name',
            array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index'  => 'name',
        ));

        $this->addColumn('type',
            array(
            'header'  => Mage::helper('catalog')->__('Type'),
            'width'   => 100,
            'index'   => 'type_id',
            'type'    => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
            'header'  => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width'   => 100,
            'index'   => 'attribute_set_id',
            'type'    => 'options',
            'options' => $sets,
        ));

        $this->addColumn('sku',
            array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width'  => 100,
            'index'  => 'sku',
        ));

        $this->addColumn('price',
            array(
            'header'        => Mage::helper('catalog')->__('Price'),
            'index'         => 'price',
            'type'          => 'currency',
            'currency_code'
            => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty',
            array(
            'header'         => Mage::helper('catalog')->__('Qty'),
            'width'          => 100,
            'index'          => 'qty',
            'type'           => 'number',
            'validate_class'
            => 'validate-number',
        ));

        $this->addColumn('category',
            array(
            'header'                    => Mage::helper('catalog')->__('Category'),
            'index'                     => 'cats',
            'type'                      => 'options',
            'validate_class'            => 'validate-number',
            'options'                   => Mage::getSingleton('mageworx_seoxtemplates/resource_categories')->getOptionArray(),
            'renderer'                  => 'MageWorx_SeoXTemplates_Block_Adminhtml_Widget_Grid_Column_Renderer_Prodcat',
            'filter_condition_callback' => array($this, 'category_filter')
        ));

        $this->addColumn('visibility',
            array(
            'header'  => Mage::helper('catalog')->__('Visibility'),
            'width'   => 70,
            'index'   => 'visibility',
            'type'    => 'options',
            'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
            'header'  => Mage::helper('catalog')->__('Status'),
            'width'   => 70,
            'index'   => 'status',
            'type'    => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Add switcher template to massaction block
     * @return MageWorx_SeoXTemplates_Block_Adminhtml_Template_Product_Edit_Tab_Conditions
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
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
                'store'    => Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getStoreId(),
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

    /**
     * Retrive attribute set ids from template model
     * @return array
     */
    protected function _getSelectedAttributesets()
    {
        return Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getInGroupItems();
    }

    public function category_filter($collection, $column)
    {
        $cond = $column->getFilter()->getCondition();
        if (empty($cond['eq'])) {
            return true;
        }

        $where = 'category.category_id = ' . $cond['eq'];
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

        if (Mage::helper('mageworx_seoxtemplates')->getStep() == 'new_step_2' || Mage::helper('mageworx_seoxtemplates')->getStep() == 'edit') {
            $duplicate = Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getAllTypeDuplicateTemplate();
            $attributes = $this->_getHtmlAttributes($duplicate);
        }

        $switcher = '
            <div class="switcher">
                <span class="switcher_title">' . Mage::helper('mageworx_seoxtemplates')->__('Assign by:') . '</span>

                <label><input type="radio"' . $attributes['disabled'] . ' ' . $attributes['title'] . ' name="general[assign_type]" value="1"' . $attributes['checkedAll'] . ' onclick="assignBy(this.value)" /> '
                . Mage::helper('mageworx_seoxtemplates')->__('All Product') .
                '</label>

                <label><input type="radio" name="general[assign_type]" value="3"' . $attributes['checkedGroup'] . ' onclick="assignBy(this.value)" /> '
                . Mage::helper('catalog')->__('Attribute Set') .
                '</label>

                <label><input type="radio" name="general[assign_type]" value="2"' . $attributes['checkedIndividual'] . ' onclick="assignBy(this.value)" /> '
                . Mage::helper('mageworx_seoxtemplates')->__('Individual Product(s)') .
                '</label>
            </div>
            <script type="text/javascript">
            //<![CDATA[
                function assignBy(type) {
                    if(type == 3) {
                        $("templateProductAttributeSet").show();
                        $("templateProductGrid").hide();
                    } else if (type == 2) {
                        $("templateProductAttributeSet").hide();
                        $("templateProductGrid").show();
                    } else {
                        $("templateProductGrid").hide();
                        $("templateProductAttributeSet").hide();
                    }
                }' . $attributes['hideIndividualJS'] . '
            //]]>
            </script>
        ';

        $switcher .= $this->_getAttributeSetHtml();

        return $switcher . parent::_toHtml();
    }

    /**
     * Retrive attribute set option array, filtered by some type templates.
     * @return array
     */
    protected function _getAttributeSetOptions()
    {
        $excludeAttributesetIds = Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getAssignForAnalogTemplateAttributesetIds();

        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId();

        $collection = Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->setEntityTypeFilter($entityTypeId);

        if(is_array($excludeAttributesetIds) && count($excludeAttributesetIds)){
            $collection->getSelect()->where('attribute_set_id NOT IN (?)', $excludeAttributesetIds);
        }

        $options = $collection->load()->toOptionArray();

        return $options;
    }

    /**
     *
     * @return string
     */
    protected function _getAttributeSetHtml()
    {
        $selectedAttributeSets = $this->_getSelectedAttributesets();
        $selectedId = (!empty($selectedAttributeSets[0])) ? $selectedAttributeSets[0] : '';

        $hint = '<div class="note"><p>' . $this->__('Note: There is only one combination "Template Type – Store View – Attribute Set" available for the chosen Product. So attribute sets assigned to different template with the same conditions are hidden.') . '</div>';

        $html =
            '<div id="templateProductAttributeSet" class="fieldset ">
                ' . $hint .  '
                <div class="hor-scroll">
                    <table class="form-list" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="label"><label for="attribute_set_id">' . Mage::helper('catalog')->__('Attribute Set') . '</label></td>
                                <td class="value">
                                    <select id="attribute_set_id" name="post_group_items" title="' . Mage::helper('catalog')->__('Attribute Set Products') . '" class=" select">';

        foreach ($this->_getAttributeSetOptions() as $option) {
            $selectedString = ($option['value'] == $selectedId) ? 'selected="selected" ' : '';
            $html .= '<option ' . $selectedString . 'value="' . $option['value'] . '">' . $option['label'] . '</option>';
        }

        $html .='
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>';
        return $html;
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
            $data['checkedGroup']      = '';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('templateProductGrid', 'templateProductAttributeSet'));
        }
        elseif (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForIndividualItems($template->getAssignType())) {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = ' checked';
            $data['checkedGroup']      = '';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('templateProductAttributeSet'));
        }
        elseif (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForGroupItems($template->getAssignType())) {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = '';
            $data['checkedGroup']      = 'checked';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('templateProductGrid'));
        }
        else {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = ' checked';
            $data['checkedGroup']      = '';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('templateProductAttributeSet'));
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
