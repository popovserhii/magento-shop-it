<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoExtended_Block_Adminhtml_System_Config_Frontend_Optimizedurls extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected $_off = false;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if(Mage::helper('mageworx_seoall/version')->isEeRewriteActive()){
            return '';
        }elseif(version_compare(Mage::getConfig()->getModuleConfig("Mage_Catalog")->version, '1.6.0.0.14', '<' )){
            $element->setComment("<b><font color = 'orange'>You use old magento version. Please see Troubleshooting section of
                                     SEO Suite's User Guide prior to enabling or disabling the setting.</font></b><br><br>" .
                                     $element->getComment());
        }
        return parent::render($element);
    }
}