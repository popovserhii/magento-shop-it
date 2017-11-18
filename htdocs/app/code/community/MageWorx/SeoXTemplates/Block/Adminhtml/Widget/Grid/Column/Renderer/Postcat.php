<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Block_Adminhtml_Widget_Grid_Column_Renderer_Postcat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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

        $allCats = Mage::helper('mageworx_seoxtemplates/template_blog')->getBlogCategoryOptionArray();

        if ($catIds && is_string($catIds)) {
            $cats = array_unique(explode(',', $catIds));
            foreach ($cats as $id) {
                if ($key = array_search($id, $allCats)) {
                    $text[] = str_replace('&nbsp;', '', $allCats[$key]);
                }
            }
        }
        return implode(', ', $text);
    }

}