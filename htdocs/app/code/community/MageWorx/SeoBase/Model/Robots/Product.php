<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Robots_Product extends MageWorx_SeoBase_Model_Robots_Abstract
{
    /**
     * Retrive robots from product attribute
     *
     * @return string|null
     */
    protected function _getRobots()
    {
        $product = Mage::registry('current_product');
        if (is_object($product) && $product->getMetaRobots()) {            
            return $product->getMetaRobots();
        }
        return $this->_getRobotsBySettings();
    }
}