<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_Hreflang extends Mage_Core_Model_Abstract
{
    protected $_hint = 'mageworx-will-replace-here-';

    public function setHreflangUrls($observer)
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

        $hreflangFactory  = Mage::getModel('mageworx_seobase/factory_action_hreflangFactory');
        $fullActionName   = $action->getFullActionName() ? $action->getFullActionName() : null;
        $hreflangModel    = $hreflangFactory->getModel($fullActionName);

        if (!$hreflangModel) {
            return;
        }

        $microtime = microtime(1);

        $hreflangUrls = $hreflangModel->getHreflangUrls();

//        var_dump($hreflangUrls); exit;

        if ($hreflangUrls && is_array($hreflangUrls)) {
            Mage::register('mageworx_alternate_urls', $hreflangUrls);

            foreach ($hreflangUrls as $hreflang => $url) {
               /**
                * We do not use addItem() method for alternate hreflang URLs because of impossibility
                * to add alternate URL the same as canonical URL. See
                */
//              $headBlock->addItem('link_rel', $hreflang,  'rel="alternate" hreflang="' . $url . '"');
                $headBlock->addItem('link_rel', $this->_hint . $hreflang,  'rel="alternate" hreflang="' . $hreflang . '"');
            }
        }
    }

    public function restoreHreflangUrls($observer)
    {
        $block = $observer->getBlock();

        if ($block->getNameInLayout() != 'root') {
            return;
        }

        $output = $observer->getTransport()->getHtml();
        if($hreflangUrls = Mage::registry('mageworx_alternate_urls')){
            foreach ($hreflangUrls as $hreflang => $url) {
                $output = str_replace('href="'. $this->_hint . $hreflang .'"', 'href="'. $url .'"', $output);
            }
            $observer->getTransport()->setHtml($output);
        }

        return;
    }
}