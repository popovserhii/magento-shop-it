<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Model_System_Config_Source_Condition
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'NewCondition', 'label' => 'New'),
            array('value' => 'RefurbishedCondition', 'label' => 'Refurbished'),
            array('value' => 'UsedCondition', 'label' => 'Used'),
            array('value' => 'DamagedCondition', 'label' => 'Damaged'),
        );
    }
}
