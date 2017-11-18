<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoMarkup_Model_System_Config_Source_ProductMethod
{
    const RICHSNIPPET_INJECTION_MICRODATA = 'injection_microdata';
    const RICHSNIPPET_JSON_LD             = 'json-ld';

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::RICHSNIPPET_INJECTION_MICRODATA,
                'label' => Mage::helper('mageworx_seomarkup')->__('HTML Injection (Microdata)')
            ),
            array(
                'value' => self::RICHSNIPPET_JSON_LD,
                'label' => Mage::helper('mageworx_seomarkup')->__('Javascript (JSON-LD)')
            ),
        );
    }

}
