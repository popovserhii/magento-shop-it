<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Block_Adminhtml_Redirect_Product_Render_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_fieldName = 'category_id';

    public function render(Varien_Object $row)
    {
        $categoryOptions = Mage::getSingleton('mageworx_seoredirects/source_category')->toOptionArray();

        if (!array_key_exists($row[$this->_fieldName], $categoryOptions)) {
            return '<font color="red">' . Mage::helper('mageworx_seoredirects')->__('Disabled or Deleted Category') . '</font>';
        }

        return ltrim($categoryOptions[$row[$this->_fieldName]], '-');
    }
}