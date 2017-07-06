<?php
/**
 * MageWorx
 * MageWorx SeoFriendlyLN Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoFriendlyLN
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoFriendlyLN_Model_Catalog_Category extends Mage_Catalog_Model_Category
{

    protected function _construct()
    {
        $this->_init('catalog/category');
    }

}
