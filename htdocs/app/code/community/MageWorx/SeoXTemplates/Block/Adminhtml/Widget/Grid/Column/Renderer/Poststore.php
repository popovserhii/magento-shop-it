<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Block_Adminhtml_Widget_Grid_Column_Renderer_Poststore extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $catIds = $row->getStoreIds();

        return array_unique(explode(', ', $catIds));
    }

}