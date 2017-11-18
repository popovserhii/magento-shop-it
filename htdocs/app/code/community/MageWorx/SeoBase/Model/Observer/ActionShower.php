<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_ActionShower
{
    public function toHtmlBlockFrontAfter($observer)
    {
        if (Mage::helper('mageworx_seobase')->showFullActionName() == 'source_code') {
            $this->showFullActionNameInSourceCode($observer);
        }
    }

    public function showFullActionNameInSourceCode($observer)
    {
        if (Mage::helper('mageworx_seobase')->showFullActionName() != 'source_code') {
            return false;
        }

        $block = $observer->getBlock();

        if ($block->getNameInLayout() == 'root') {

            $output = $observer->getTransport()->getHtml();

            if ($actionName = Mage::helper('mageworx_seobase')->getCurrentFullActionName()) {
                $comment = "<!--MageWorx_SeoBase: ACTION NAME IS '" . $actionName ."'-->";
                $output = str_replace('</head>', $comment . "\n" . '</head>', $output);
            }

            $observer->getTransport()->setHtml($output);
        }
    }
}