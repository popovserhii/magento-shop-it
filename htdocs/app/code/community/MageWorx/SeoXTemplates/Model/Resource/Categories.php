<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Model_Resource_Categories extends Varien_Object
{
    private $_options = array();
    
    public function getOptionArray()
    {
        if (count($this->_options)) {
            return $this->_options;
        }

        foreach (Mage::app()->getWebsites() as $website) {
            if($website->getDefaultStore()){
                $rootId = $website->getDefaultStore()->getRootCategoryId();
                $rootCat = Mage::getModel('catalog/category')->load($rootId);
                $this->_options[$rootId] = $rootCat->getName();
                $this->getChildCats($rootCat, 0);
            }
        }

        return $this->_options;
    }

    public function getChildCats($cat, $level)
    {
        if ($children = $cat->getChildren()) {
            $level++;
            $children = explode(',', $children);
            foreach ($children as $childId) {
                $childCat = Mage::getModel('catalog/category')->load($childId);
                $this->_options[$childId] = str_repeat('&nbsp;&nbsp;&nbsp;', $level) . $childCat->getName();
                if ($childCat->getChildren()) {
                    $this->getChildCats($childCat, $level);
                }
            }
        }
    }
}