<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_System_Config_Source_Cms_Canonical
{
    protected $_options;

    public function toOptionArray()
    {
        $this->_options = array(
            array(
                'value' => '0',
                'label' => Mage::helper('mageworx_seobase')->__('Use Root CMS Page URL')
            ),
            array(
                'value' => '1',
                'label' => Mage::helper('mageworx_seobase')->__('Use Current CMS Page URL')
            ),
        );
        return $this->_options;
    }

}