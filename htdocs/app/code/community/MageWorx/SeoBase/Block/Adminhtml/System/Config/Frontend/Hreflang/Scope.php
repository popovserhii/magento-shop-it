<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Adminhtml_System_Config_Frontend_Hreflang_Scope extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_off = false;

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if ($this->_off) {
            return '';
        }
        return parent::_getElementHtml($element);
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            $element->setComment('');
            $this->_off = true;
        }
        return parent::render($element);
    }
}