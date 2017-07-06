<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Factory_Action_HreflangFactory extends MageWorx_SeoBase_Model_Factory_Action_Abstract
{
    public function getModel($fullActionName = null)
    {
        $fullActionName = $fullActionName ? $fullActionName : Mage::helper('mageworx_seobase')->getCurrentFullActionName();

        switch($fullActionName) {

            case 'catalog_category_view':
                $modelUri = 'mageworx_seobase/hreflang_category';
                break;
            case 'catalog_product_view':
                $modelUri = 'mageworx_seobase/hreflang_product';
                break;
            case 'cms_index_index':
                $modelUri = 'mageworx_seobase/hreflang_homePage';
                break;
            case 'cms_page_view':
                $modelUri = 'mageworx_seobase/hreflang_page';
                break;
        }

        return (!empty($modelUri)) ? Mage::getModel($modelUri) : null;
    }

}