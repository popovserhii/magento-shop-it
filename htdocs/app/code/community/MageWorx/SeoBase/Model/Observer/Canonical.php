<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_Canonical extends Mage_Core_Model_Abstract
{
    public function setCanonicalUrl($observer)
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

        if ($headBlock && is_a($headBlock, 'Mage_Page_Block_Html_Head')) {
            $canonicalFactory = Mage::getModel('mageworx_seobase/factory_action_canonicalFactory');
            $fullActionName = $action->getFullActionName() ? $action->getFullActionName() : null;
            $canonicalUrl = $canonicalFactory->getModel($fullActionName)->getCanonicalUrl();

            Mage::log($canonicalUrl, null, 'canonical.log');

            if ($canonicalUrl) {
                $headBlock->addItem('link_rel', $canonicalUrl,  'rel="canonical"');
            }
        }
    }
}