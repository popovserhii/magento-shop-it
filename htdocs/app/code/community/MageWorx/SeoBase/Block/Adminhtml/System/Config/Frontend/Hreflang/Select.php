<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Block_Adminhtml_System_Config_Frontend_Hreflang_Select extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return str_replace('<select', '<select style="width:auto"',$element->getElementHtml());
    }

}