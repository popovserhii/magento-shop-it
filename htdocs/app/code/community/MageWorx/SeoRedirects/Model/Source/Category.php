<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class Mageworx_SeoRedirects_Model_Source_Category
{
    protected $_options;

    /**
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_options == null) {
            $options = array();
            foreach ($this->_getCategoryTree() as $category) {
                $options[$category['value']] =  $category['label'];
            }
            $this->_options = $options;
        }

        return array('' => '') + $this->_options;
    }

    /**
     *
     * @param Varien_Data_Tree_Node $node
     * @param array $values
     * @param int $level
     * @return array
     */
    protected function _createCategoryTree(Varien_Data_Tree_Node $node, $values, $level = 0)
    {
        $level++;

        $values[$node->getId()]['value'] =  $node->getId();
        $values[$node->getId()]['label'] = str_repeat("--", $level) . $node->getName();

        foreach ($node->getChildren() as $child) {
            $values = $this->_createCategoryTree($child, $values, $level);
        }

        return $values;
    }

    /**
     *
     * @return array
     */
    protected function _getCategoryTree()
    {
        $store    = Mage::app()->getFrontController()->getRequest()->getParam('store', 0);
        $parentId = $store ? Mage::app()->getStore($store)->getRootCategoryId() : 1;

        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();
        $root = $tree->getNodeById($parentId);

        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($store)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return $this->_createCategoryTree($root, array());
    }
}
