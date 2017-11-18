<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */



class MageWorx_SeoReports_Block_Adminhtml_Grid_Filter_Error extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{

    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }
        return $this->getValue();
    }

}
