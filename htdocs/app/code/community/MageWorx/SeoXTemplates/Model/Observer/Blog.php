<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Observer_Blog extends Mage_Core_Model_Abstract
{
    /**
     * Convert properties of the product that contain [category] and [categories]
     *
     * @param type $observer
     * @return type
     */
    public function deleteBlogTemplateRelations($observer)
    {
        if(is_object($observer->getData('object')) && ($observer->getData('object') instanceof AW_Blog_Model_Blog)){
            $blogpost = $observer->getData('object');
            $blogpostId = $blogpost->getPostId();

            $relationCollection = Mage::getModel('mageworx_seoxtemplates/template_relation_blog')->getCollection();
            $relationCollection->loadByItemId($blogpostId);
            foreach($relationCollection as $relation){
                $relation->delete();
            }
        }
    }
}