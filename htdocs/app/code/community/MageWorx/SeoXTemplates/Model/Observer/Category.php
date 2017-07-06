<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Category extends Mage_Core_Model_Abstract
{
    protected $_convertedTitle;

    protected $_convertedMetaDescription;

    protected $_convertedDescription;

    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updateCategoryProperties($observer)
    {
        $category = $observer->getData('category');

        if (Mage::helper('mageworx_seoxtemplates/config')->isUseCategorySeoName() && $category->getCategorySeoName()) {
            $category->setName($category->getCategorySeoName());
        }
    }

    /**
     * Modify category data and meta head
     * Event: core_block_abstract_to_html_before
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function modifyCategoryAndHead($observer)
    {
        /** @var Mage_Page_Block_Html_Head $block */
        $block = $observer->getBlock();

        if ($block->getNameInLayout() != 'head') {
            return false;
        }

        if ('catalog_category_view' == Mage::helper('mageworx_seoall/request')->getCurrentFullActionName()) {

            $category = Mage::registry('current_category');

            if (is_object($category)) {
                $dynamicRenderer = Mage::getSingleton('mageworx_seoxtemplates/dynamicRenderer_category');
                $dynamicRenderer->modifyCategoryTitle($category, $block);
                $dynamicRenderer->modifyCategoryMetaDescription($category, $block);
                $dynamicRenderer->modifyCategoryDescription($category);
            }
        }
    }
}
