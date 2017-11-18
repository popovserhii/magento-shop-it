<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Observer_Category
{
    /**
     * Modify category description
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updateCategoryProperties($observer)
    {
        //catalog_category_load_after
        $category = $observer->getData('category');

        if ($this->_out($category)) {
            return;
        }

        //$microtime = microtime(1);

        $html            = $category->getDescription();
        $maxReplaceCount = Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForCategoryPage();

        $pairWidget       = array();
        $htmlWidgetCroped = Mage::getModel('mageworx_seocrosslinks/widget_filter')->replace($html, $pairWidget);

        $htmlModifyWidgetCroped = Mage::getSingleton('mageworx_seocrosslinks/crosslink')
            ->replace('category', $htmlWidgetCroped, $maxReplaceCount, null, $category->getId());

        if ($htmlModifyWidgetCroped) {
            $htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlModifyWidgetCroped);
            $category->setDescription($htmlModify);
        }

        //echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>";
    }

    /**
     * Check if go out
     *
     * @param AW_Blog_Model_Post $post
     * @return boolean
     */
    protected function _out($category)
    {
        if (!Mage::helper('mageworx_seocrosslinks')->isEnabled()) {
            return true;
        }

        if (Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForCategoryPage() == 0) {
            return true;
        }

        if (!is_object($category)) {
            return true;
        }

        if ($category->getExcludeFromCrosslinking()) {
            return true;
        }

        if (!$category->getDescription()) {
            return true;
        }

        if (!in_array(Mage::helper('mageworx_seoall/request')->getCurrentFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($category->getId() != Mage::app()->getRequest()->getParam('id')) {
            return true;
        }

        return false;
    }

    /**
     * Retrive list of available actions
     *
     * @return array
     */
    protected function _getAvailableActions()
    {
        return array('catalog_category_view');
    }

}
