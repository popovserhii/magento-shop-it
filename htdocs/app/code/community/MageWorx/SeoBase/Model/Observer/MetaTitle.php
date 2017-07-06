<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_MetaTitle extends Mage_Core_Model_Abstract
{
    protected $_out = false;

    public function addMetaTitle($observer)
    {
        $action = $observer->getAction();
        $layout = $observer->getLayout();

        if (!$layout || !$action) {
            return;
        }

        $request = Mage::app()->getRequest();
        if ($request && $request->isXmlHttpRequest()) {
            return;
        }

        $headBlock = $layout->getBlock('head');

        if (!$headBlock) {
            return;
        }
        
        $title = $this->_getCmsMetaTitle();

        if ($title) {
            $headBlock->setData('title', $title);
        }
    }

    protected function _getCmsMetaTitle()
    {
        if (Mage::getSingleton('cms/page')->getMetaTitle()) {
            $title = Mage::getSingleton('cms/page')->getMetaTitle();
        }        
        return !empty($title) ? $title : null;
    }
}