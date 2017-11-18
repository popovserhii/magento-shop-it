<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Robots_Page extends MageWorx_SeoBase_Model_Robots_Abstract
{
    protected function _getRobots()
    {
        $page = Mage::getSingleton('cms/page');        
        
        if (is_object($page) && $page->getMetaRobots()) {
            return $page->getMetaRobots();
        }
        return $this->_getRobotsBySettings();
    }
}