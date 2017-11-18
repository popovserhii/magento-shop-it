<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Block_Adminhtml_System_Config_Frontend_Richsnippet extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_off = false;

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        if ($this->_off) {
            return $this->__('Richsnippets is available since Magento 1.4.1.0.');
        }
        return parent::_getElementHtml($element);
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (version_compare(Mage::getVersion(), '1.4.1.0', '<'))
        {
            $element->setComment('');
            $this->_off = true;
        }

        return parent::render($element);
    }
}