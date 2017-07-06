<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Seoname extends Mage_Core_Model_Abstract
{
    /**
     * Replace product name with product seo name if exists
     * 
     * @param type $observer
     * @return type
     */
    public function replaceName($observer)
    {
        $accessToPage = array(
            'catalog_product_view',
            'review_product_list',
        );

        if(!in_array(Mage::helper('mageworx_seoall/request')->getCurrentFullActionName(), $accessToPage)){

            $block = $observer->getBlock();
            if($block->getNameInLayout() != 'product.info'){
                return;
            }

            $productSeoName = Mage::registry('current_product')->getProductSeoName();
            if(!$productSeoName){
                return;
            }

            $transport    = $observer->getTransport();
            $normalOutput = $observer->getTransport()->getHtml();
            $modifyOutput = $normalOutput;
            $h1Tags = array();
            preg_match_all('#<h1[^>]*>(.*)?</h1>#is', $normalOutput, $h1Tags);

            for ($i = 0; $i < count($h1Tags[1]); $i++) {
                $modifyOutput  = str_replace($h1Tags[1][$i], $productSeoName, $modifyOutput);
            }

            $transport->setHtml($modifyOutput);
        }
    }
}