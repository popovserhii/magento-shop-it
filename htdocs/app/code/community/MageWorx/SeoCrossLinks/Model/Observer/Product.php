<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Observer_Product
{
    /**
     * Modify product short description and description
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function updateProductProperties($observer)
    {
        //catalog_product_load_after
        $product = $observer->getData('product');

        if ($this->_out($product)) {
            return;
        }

        //$microtime = microtime(1);

        $attributes       = Mage::helper('mageworx_seocrosslinks')->getProductAttributesForReplace();
        $maxReplaceCount  = Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForProductPage();
        $shortDescription = in_array('short_description', $attributes) ? $product->getShortDescription() : '';
        $description      = in_array('description', $attributes) ? $product->getDescription() : '';

        if(!$shortDescription && !$description){
            return;
        }

        $glue = '...16d8939...';

        $html = $shortDescription . $glue . $description;

		$pairWidget       = array();
        $htmlWidgetCroped = Mage::getModel('mageworx_seocrosslinks/widget_filter')->replace($html, $pairWidget);

        $htmlModifyWidgetCroped = Mage::getSingleton('mageworx_seocrosslinks/crosslink')
            ->replace('product', $htmlWidgetCroped, $maxReplaceCount, $product->getSku());

        if ($htmlModifyWidgetCroped && strpos($htmlModifyWidgetCroped, $glue) !== false) {

			$htmlModify = str_replace(array_keys($pairWidget), array_values($pairWidget), $htmlModifyWidgetCroped);

            list($shortDescriptionMod, $descriptionMod) = explode($glue, $htmlModify);

            if (!empty($shortDescriptionMod) && $shortDescription) {
                $product->setShortDescription($shortDescriptionMod);
            }

            if (!empty($descriptionMod) && $description) {
                $product->setDescription($descriptionMod);
            }
        }

        //echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>";
    }

    /**
     * Check if go out
     *
     * @param $product
     * @return boolean
     */
    protected function _out($product)
    {
        if (!Mage::helper('mageworx_seocrosslinks')->isEnabled()) {
            return true;
        }

        if (Mage::helper('mageworx_seocrosslinks')->getReplacemenetCountForProductPage() == 0) {
            return true;
        }

        if (!count(Mage::helper('mageworx_seocrosslinks')->getProductAttributesForReplace())) {
            return true;
        }

        if (!is_object($product)) {
            return true;
        }

        if ($product->getExcludeFromCrosslinking()) {
            return true;
        }

        if (!in_array(Mage::helper('mageworx_seoall/request')->getCurrentFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        if ($product->getId() != Mage::app()->getRequest()->getParam('id')) {
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
        return array('catalog_product_view', 'review_product_page');
    }

}
