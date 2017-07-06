<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Factory_Action_RobotsFactory extends MageWorx_SeoBase_Model_Factory_Action_Abstract
{
    public function getModel($fullActionName = null)
    {
        $fullActionName = $fullActionName ? $fullActionName : Mage::helper('mageworx_seobase')->getCurrentFullActionName();

        switch($fullActionName) {

            case 'catalog_category_view':
                $modelUri = 'mageworx_seobase/robots_category';
                break;
            case 'catalog_product_view':
                $modelUri = 'mageworx_seobase/robots_product';
                break;
            case 'tag_product_list':
            case 'cms_index_index':
                $modelUri = null;
            case 'cms_index_noRoute':
            case 'cms_page_view':
                $modelUri = 'mageworx_seobase/robots_page';
                break;
            default:
                $modelUri = 'mageworx_seobase/robots_default';
        }

        return Mage::getModel($modelUri);
    }

}