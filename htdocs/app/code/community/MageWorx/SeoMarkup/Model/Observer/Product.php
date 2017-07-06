<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoMarkup_Model_Observer_Product
{
    public function createRichsnippetProductMarkup($observer)
    {
        if (!Mage::helper('mageworx_seomarkup')->isProductPage()) {
            return;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isProductRichsnippetEnabled()) {
            return false;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isProductInjectionMicrodataMethod()) {
            return;
        }

        $block = $observer->getBlock();

        if ($this->_getHandlerForBlock($block->getNameInLayout())) {

            $transport    = $observer->getTransport();
            $normalOutput = $observer->getTransport()->getHtml();
            $modelUri     = $this->_getHandlerForBlock($block->getNameInLayout());

            $model        = Mage::getModel($modelUri);

            $modifyOutput = $model->render($normalOutput, $block, true);
            if ($modifyOutput) {
                $transport->setHtml($modifyOutput);
            }
        }
        return $this;
    }

    public function createRichsnippetReviewMarkup($observer)
    {
        if (Mage::getStoreConfigFlag('advanced/modules_disable_output/Mage_Review')) {
            return;
        }

        if (!Mage::helper('mageworx_seomarkup')->isProductPage()) {
            return;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isProductRichsnippetEnabled()) {
            return;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isProductInjectionMicrodataMethod()) {
            return;
        }
        $block = $observer->getBlock();

        if ($block instanceof Mage_Review_Block_Helper) {
            $transport    = $observer->getTransport();
            $normalOutput = $observer->getTransport()->getHtml();

            $model = Mage::getModel('mageworx_seomarkup/richsnippet_catalog_product_review');

            $modifyOutput = $model->render($normalOutput, $block, true);
            if ($modifyOutput) {
                $transport->setHtml($modifyOutput);
            }
        }
    }

    /**
     * @param type $blockName
     * @return mixed
     */
    protected function _getHandlerForBlock($blockName)
    {
        $handlers = array(
            'product.info'  => 'mageworx_seomarkup/richsnippet_catalog_product_product',
        );

        if (!empty($handlers[$blockName])) {
            return $handlers[$blockName];
        }
        return null;
    }
}
