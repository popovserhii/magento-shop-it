<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Observer_AwBlog
{
    /**
     * Replace keywords to links in AW blog content
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updateBlogContent($observer)
    {
        //model_load_after
        $post = $observer->getObject();

        if ($this->_out($post)) {
            return;
        }
        //$microtime = microtime(1);

        $html             = $post->getPostContent();
        $pairWidget       = array();
        $htmlWidgetCroped = Mage::getModel('mageworx_seocrosslinks/widget_filter')->replace($html, $pairWidget);

        $maxReplaceCount = Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForBlogPage();

        $htmlRaw = Mage::getSingleton('mageworx_seocrosslinks/crosslink')
            ->replace('aw_blog', $htmlWidgetCroped, $maxReplaceCount);

        if ($htmlRaw) {
            $htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlRaw);
            $post->setPostContent($htmlModify);
        }
        //Mage::log(number_format((microtime(1) - $microtime), 5), null, 'cl.log');
    }

    /**
     * Check if go out
     *
     * @param AW_Blog_Model_Post $post
     * @return boolean
     */
    protected function _out($post)
    {
        if (!Mage::helper('mageworx_seocrosslinks')->isEnabled()) {
            return true;
        }

        if (Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForBlogPage() == 0) {
            return true;
        }

        if (!is_object($post)) {
            return true;
        }

        if (!($post instanceof AW_Blog_Model_Post)) {
            return true;
        }

        if (!$post->getPostContent()) {
            return true;
        }

        if (!$this->_getIsAwBlogPage()) {
            return true;
        }

        if ($post->getData('identifier') != Mage::app()->getRequest()->getParam('identifier')) {
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
        return array('blog_post_view');
    }

    /**
     * Check if AW blog page now
     *
     * @return boolean
     */
    protected function _getIsAwBlogPage()
    {
        if (!Mage::getConfig()->getModuleConfig('AW_Blog')->is('active', 'true')) {
            return false;
        }
        if (in_array(Mage::helper('mageworx_seoall/request')->getCurrentFullActionName(), $this->_getAvailableActions())) {
            return true;
        }
        return false;
    }
}
