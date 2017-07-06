<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Block_Adminhtml_Template_Category_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_categoryIds;
    protected $_selectedNodes    = null;
    protected $_readonlyIds      = array();
    protected $_readonlyFlag     = false;
    protected $_withProductCount = false;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mageworx/seoxtemplates/categories.phtml');
    }

    /**
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();

            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setLoadProductCount($this->_withProductCount);

            $this->setData('category_collection', $collection);
        }

        return $collection;
    }

    /**
     *
     * @param null|Varien_Data_Tree_Node $node
     * @return boolean
     */
    public function isReadonly($node = null)
    {
        $readonlyIds = $this->_getAssignForAnalogTemplateCategoryIds();
        if (!is_null($node) && in_array((string) $node->getData('entity_id'), $readonlyIds)) {
            return true;
        }

        return false;
    }

    /**
     * Retrive product ids by template with some type and store view
     * @return array
     */
    protected function _getAssignForAnalogTemplateCategoryIds()
    {
        if (false == $this->_readonlyFlag) {
            $readonlyIds         = Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getAssignForAnalogTemplateCategoryIds();
            $this->_readonlyFlag = true;
            $this->_readonlyIds  = $readonlyIds;
        }
        return $this->_readonlyIds;
    }

    /**
     * Retrive category ids
     * @return array
     */
    protected function getCategoryIds()
    {
        $categoryIds = array();
        $collection  = Mage::getResourceModel('mageworx_seoxtemplates/template_relation_category_collection')->loadByTemplateId(Mage::app()->getRequest()->getParam('template_id',
                0));

        foreach ($collection as $item) {
            $categoryIds[] = $item->getCategoryId();
        }
        return $categoryIds;
    }

    /**
     * Retrive category ids as string
     * @return string
     */
    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }

    /**
     * Retrive root node
     * @return Varien_Data_Tree_Node
     */
    public function getRootNode()
    {
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getCategoryIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    /**
     * Retrive root node
     * @return Varien_Data_Tree_Node
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {

            $storeId = $this->_getStoreId();

            if ($this->getRequest()->getParam('isAjax')) {
                $storeId = null;
            }

            if ($storeId) {
                $store  = Mage::app()->getStore($storeId);
                $rootId = $store->getRootCategoryId();
            }
            else {
                $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }

            $ids  = $this->getSelectedCategoriesPathIds($rootId);
            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->loadByIds($ids, false, false);

            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getCategoryCollection());

            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setIsVisible(true);
                if ($this->isReadonly($root)) {
                    $root->setDisabled(true);
                }
            }
            elseif ($root && $root->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $root->setName(Mage::helper('catalog')->__('Root'));
            }

            Mage::register('root', $root);
        }

        return $root;
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Varien_Data_Tree_Node|array $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);

        $isParent = $this->_isParentSelectedCategory($node);

        if ($isParent) {
            $item['expanded'] = true;
        }

        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }

        if ($this->isReadonly($node)) {
            $item['disabled'] = true;
        }
        return $item;
    }

    /**
     * Returns whether $node is a parent (not exactly direct) of a selected node
     *
     * @param Varien_Data_Tree_Node $node
     * @return bool
     */
    protected function _isParentSelectedCategory($node)
    {
        foreach ($this->_getSelectedNodes() as $selected) {
            if ($selected) {
                $pathIds = explode('/', $selected->getPathId());
                if (in_array($node->getId(), $pathIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns array with nodes those are selected (contain current product)
     *
     * @return array
     */
    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            foreach ($this->getCategoryIds() as $categoryId) {
                $this->_selectedNodes[] = $this->getRoot()->getTree()->getNodeById($categoryId);
            }
        }

        return $this->_selectedNodes;
    }

    /**
     * Returns JSON-encoded array of category children
     *
     * @param int $categoryId
     * @return string
     */
    public function getCategoryChildrenJson($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node     = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);

        if (!$node || !$node->hasChildren()) {
            return '[]';
        }

        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }

        return Zend_Json::encode($children);
    }

    /**
     * Get URL for categories tree ajax loader
     *
     * @return string
     */
    public function getLoadTreeUrl($expanded=null)
    {
        $params = array('_current' => true, 'type_id'  => $this->_getTypeId(), 'store'    => $this->_getStoreId());

        return $this->getUrl('*/*/categoriesJson', $params);
    }

    /**
     * Return distinct path ids of selected categories
     *
     * @param int $rootId Root category Id for context
     * @return array
     */
    public function getSelectedCategoriesPathIds($rootId = false)
    {
        $ids        = array();
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $this->getCategoryIds()));

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    /**
     * Retrive HTML attributes for switcher
     * @param MageWorx_SeoXTemplates_Model_Mysql4_Template_Category_Collection $duplicate|null
     * @return array
     */
    protected function _getHtmlAttributes($duplicate = null)
    {
        $data = array();

        if (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForAllItems($this->_getAssignType())) {

            $data['checkedAll']        = ' checked';
            $data['checkedIndividual'] = '';
            $data['checkedGroup']      = '';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('grop_fields'));
        }
        elseif (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForIndividualItems($this->_getAssignType())) {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = ' checked';
            $data['checkedGroup']      = '';
            $data['hideIndividualJS']  = '';
        }
        elseif (Mage::helper('mageworx_seoxtemplates/factory')->getHelper()->isAssignForGroupItems($this->_getAssignType())) {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = '';
            $data['checkedGroup']      = 'checked';
            $data['hideIndividualJS']  = Mage::helper('mageworx_seoxtemplates')->getHideElementJsString(array('grop_fields'));
        }
        else {
            $data['checkedAll']        = '';
            $data['checkedIndividual'] = ' checked';
            $data['checkedGroup']      = '';
            $data['hideIndividualJS']  = '';
        }

        if ($duplicate) {
            $data['disabled'] = ' disabled';
            $data['title']    = ' title="' . Mage::helper('mageworx_seoxtemplates')->__('You have already created a similar template (%s), which assigned for all product/categories of the template store(s).',
                    $duplicate->getName()) . '"';
        }
        else {
            $data['disabled'] = '';
            $data['title']    = '';
        }
        return $data;
    }

    /**
     * Add switcher "All Products / Products From Grid"
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
            . Mage::helper('mageworx_seoxtemplates')->__('All Categories') .
            '</label>
                <label><input type="radio" name="general[assign_type]" value="2"' . $attributes['checkedIndividual'] . ' onclick="assignBy(this.value)" /> '
            . Mage::helper('mageworx_seoxtemplates')->__('Category Tree') .
            '</label>
            </div>
            <script type="text/javascript">
            //<![CDATA[
                function assignBy(type) {
                    if (type == 2) {
                        $("grop_fields").show();
                    } else {
                        $("grop_fields").hide();
                    }
                }' . $attributes['hideIndividualJS'] . '
            //]]>
            </script>
        ';
        return $switcher . parent::_toHtml();
    }

    /**
     *
     * @return int
     */
    public function _getStoreId()
    {
        return Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getStoreId();
    }

    /**
     *
     * @return int
     */
    public function _getTypeId()
    {
        return Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getTypeId();
    }

    /**
     *
     * @return int
     */
    public function _getAssignType()
    {
        return Mage::helper('mageworx_seoxtemplates/factory')->getModel()->getAssignType();
    }

    /**
     *
     * @return int
     */
    public function _getTemplateId()
    {
        $template = Mage::helper('mageworx_seoxtemplates/factory')->getModel();

        if (is_object($template) && $template->getTemplateId()) {
            return $template->getTemplateId();
        }
//        elseif ($templateId = $this->getRequest()->getParam('template_id')) {
//            return $templateId;
//        }
    }

}