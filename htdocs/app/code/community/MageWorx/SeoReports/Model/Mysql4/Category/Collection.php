<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('seoreports/category');
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'meta_title_error') {
            if ($condition == 'missing') {
                $field     = 'prepared_meta_title';
                $condition = array('eq' => '');
            }
            elseif ($condition == 'long') {
                $field     = 'meta_title_len';
                $condition = array('gt' => '70');
            }
            elseif ($condition == 'duplicate') {
                $field     = 'meta_title_dupl';
                $condition = array('gt' => '1');
            }
        }
        elseif ($field == 'name_error') {
            if ($condition == 'duplicate') {
                $field     = 'name_dupl';
                $condition = array('gt' => '1');
            }
        }
        elseif ($field == 'meta_descr_error') {
            if ($condition == 'missing') {
                $field     = 'meta_descr_len';
                $condition = array('eq' => '0');
            }
            elseif ($condition == 'long') {
                $field     = 'meta_descr_len';
                $condition = array('gt' => '150');
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

}