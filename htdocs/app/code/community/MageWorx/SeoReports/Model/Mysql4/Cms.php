<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Model_Mysql4_Cms extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('seoreports/cms', 'entity_id');
    }

}