<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_NextPrev extends Mage_Core_Model_Abstract
{
    public function addNextPrev($observer)
    {
        $helperData = Mage::helper('mageworx_seobase');

        if ($helperData->getStatusLinkRel() == MageWorx_SeoBase_Model_NextPrev_Abstract::DISABLE_NEXT_PREV) {
            return;
        }
        $block = $observer->getBlock();

        if ($block->getNameInLayout() != 'root') {
            return;
        }

        $output     = $observer->getTransport()->getHtml();
        $actionName = Mage::helper('mageworx_seobase')->getCurrentFullActionName();

        if (substr_count($output, '</head>') != 1) {
            return;
        }

        $nextPrevModel = Mage::getModel('mageworx_seobase/factory_action_nextPrevFactory')->getModel($actionName);
        if (!is_object($nextPrevModel)) {
            return;
        }

        $prevUrl = $nextPrevModel->getPrevUrl();
        $nextUrl = $nextPrevModel->getNextUrl();

        if (!empty($prevUrl)) {
            $prevStr = '<link rel="prev" href="' . $prevUrl . '" />';
            $output = str_ireplace('</head>', "\n" . $prevStr . '</head>', $output);
        }

        if (!empty($nextUrl)) {
            $nextStr = '<link rel="next" href="' . $nextUrl . '" /> ';
            $output = str_ireplace('</head>', "\n" . $nextStr . '</head>', $output);
        }

        $observer->getTransport()->setHtml($output);

        return;
    }

    protected function _nextPrevOut($pager)
    {
        $availableLimit = $pager->getAvailableLimit();

        if (!is_array($availableLimit)) {
            return true;
        }
    }
}