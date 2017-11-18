<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Factory_Action_CanonicalFactory extends MageWorx_SeoBase_Model_Factory_Action_Abstract
{
    public function getModel($fullActionName = null)
    {
        $fullActionName = $fullActionName ? $fullActionName : Mage::helper('mageworx_seobase')->getCurrentFullActionName();

        switch($fullActionName) {

            case 'catalog_category_view':
                $modelUri = 'mageworx_seobase/canonical_category';
                break;
            case 'catalog_product_view':
                $modelUri = 'mageworx_seobase/canonical_product';
                break;
            case 'review_product_list':
                if (Mage::helper('mageworx_seobase')->isProductCanonicalUrlOnReviewPage()) {
                    $modelUri = 'mageworx_seobase/canonical_product';
                } else {
                    $modelUri = 'mageworx_seobase/canonical_review';
                }
                break;
            case 'tag_product_list':
                $modelUri = 'mageworx_seobase/canonical_tag';
                break;
            case 'cms_index_index':
            case 'cms_index_defaultIndex':
                $modelUri = 'mageworx_seobase/canonical_homePage';
                break;
            case 'cms_page_view':
                $modelUri = 'mageworx_seobase/canonical_page';
                break;
            case 'cms_index_noRoute':
                $modelUri = 'mageworx_seobase/canonical_noroute';
                break;
            default:
                $modelUri = 'mageworx_seobase/canonical_default';
        }

        return Mage::getSingleton($modelUri);
    }

}