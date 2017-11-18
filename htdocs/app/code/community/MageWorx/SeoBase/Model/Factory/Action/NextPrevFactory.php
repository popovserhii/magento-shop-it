<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Factory_Action_NextPrevFactory extends MageWorx_SeoBase_Model_Factory_Action_Abstract
{
    public function getModel($fullActionName = null)
    {
        $fullActionName = $fullActionName ? $fullActionName : Mage::helper('mageworx_seobase')->getCurrentFullActionName();

        switch($fullActionName) {

            case 'catalog_category_view':
                $modelUri = 'mageworx_seobase/nextPrev_category';
                break;          
            case 'tag_product_list':
                $modelUri = 'mageworx_seobase/nextPrev_tag';
                break;
            case 'review_product_list':
                $modelUri = 'mageworx_seobase/nextPrev_review';
                break;
            case 'catalogsearch_result_index':
                $modelUri = 'mageworx_seobase/nextPrev_search';
                break;            
        }

        return !empty($modelUri) ? Mage::getModel($modelUri) : null;
    }

}