<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_Crossdomain extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    protected $_options;

    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $stores           = Mage::app()->getStores();
            $this->_options[] = array('value' => '', 'label' => 'Default Store URL');
            foreach ($stores as $store) {
                /* @var $store Mage_Core_Model_Store */
                $this->_options[] = array('value' => $store->getId(), 'label' => $store->getName() . ' — ' . $store->getBaseUrl());
            }
        }
        return $this->_options;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

}