<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_System_Config_Source_Store_Global extends MageWorx_SeoBase_Model_System_Config_Source_Store
{
    protected $_options;

    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        array_unshift($options, array('label'=> Mage::helper('adminhtml')->__('--Please Select--'), 'value' => '0'));
        return $options;
    }

    protected function _cmp($a, $b)
    {
        //$orderBy = array('website_sort_order' => 'desc', 'website_id' => 'asc', 'store_sort_order' => 'desc', 'value' => 'asc');
        $orderBy = array('website_id' => 'asc', 'value' => 'asc');
        $result = 0;
        foreach ($orderBy as $key => $value) {
            if ($a[$key] == $b[$key]) continue;
            $result = ($a[$key] < $b[$key]) ? -1 : 1;
            if ($value == 'desc') $result = -$result;
            break;
        }
        return $result;
    }
}