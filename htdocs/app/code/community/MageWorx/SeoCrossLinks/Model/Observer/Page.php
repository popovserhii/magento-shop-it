<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Observer_Page
{
    /**
     * Modify CMS page content
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updatePageContent($observer)
    {
        //cms_page_load_after
        $page = $observer->getObject();

        if ($this->_out($page)) {
            return;
        }

        //$microtime = microtime(1);

        $html             = $page->getContent();
        $pairWidget       = array();
        $htmlWidgetCroped = Mage::getModel('mageworx_seocrosslinks/widget_filter')->replace($html, $pairWidget);

        $maxReplaceCount = Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForCmsPage();

        $htmlModifyWidgetCroped = Mage::getSingleton('mageworx_seocrosslinks/crosslink')
            ->replace('cms_page', $htmlWidgetCroped, $maxReplaceCount);

        if ($htmlModifyWidgetCroped) {
            $htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlModifyWidgetCroped);
            $page->setContent($htmlModify);
        }

        //Mage::log(number_format((microtime(1) - $microtime), 5), null, 'cl.log');
    }

    /**
     * Check if go out
     *
     * @param Mage_Cms_Model_Page $page
     * @return boolean
     */
    protected function _out($page)
    {
        if (!Mage::helper('mageworx_seocrosslinks')->isEnabled()) {
            return true;
        }

        if (Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForCmsPage() == 0) {
            return true;
        }

        if (!is_object($page)) {
            return true;
        }

        if ($page->getExcludeFromCrosslinking()) {
            return true;
        }

        if (!$page->getContent()) {
            return true;
        }

        if (!in_array(Mage::helper('mageworx_seoall/request')->getCurrentFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($page->getId() != Mage::app()->getRequest()->getParam('page_id')) {
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
        return array('cms_page_view');
    }
}
