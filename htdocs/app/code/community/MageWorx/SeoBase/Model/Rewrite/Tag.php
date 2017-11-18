<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_Rewrite_Tag extends Mage_Tag_Model_Tag
{

    public function getTaggedProductsUrl()
    {
        return Mage::getUrl('tag/', array('_nosid' => true)) . urlencode($this->getName());
    }

}