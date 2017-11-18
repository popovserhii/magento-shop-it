<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_Robots extends MageWorx_SeoBase_Model_Catalog_Product_Attribute_Source_Meta_Robots
{

    protected $_options;

    public function toOptionArray()
    {
        $this->_options = parent::getAllOptions();
        array_unshift($this->_options, array('value' => '', 'label' => 'Use Default'));
        return $this->_options;
    }

}