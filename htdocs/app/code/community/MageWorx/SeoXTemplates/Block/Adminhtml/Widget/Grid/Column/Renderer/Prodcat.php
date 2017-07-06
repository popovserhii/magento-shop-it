<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Block_Adminhtml_Widget_Grid_Column_Renderer_Prodcat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $text = array();
        $catIds = $row->getCatIds();

        $allCats = Mage::getSingleton('mageworx_seoxtemplates/resource_categories')->getOptionArray();

        if ($catIds && is_string($catIds)) {
            foreach (explode(',', $catIds) as $id) {
                if (isset($allCats[$id])) {
                    $text[] = str_replace('&nbsp;', '', $allCats[$id]);
                }
            }
        }
        return implode(', ', $text);
    }
}