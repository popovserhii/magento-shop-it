<?php
/**
 * MageWorx
 * MageWorx SeoSuite Ultimate Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoSuiteUltimate
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoSuiteUltimate_Block_Adminhtml_System_Config_Fieldset_About
    extends    Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'mageworx/seoult/about.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
}

?>
