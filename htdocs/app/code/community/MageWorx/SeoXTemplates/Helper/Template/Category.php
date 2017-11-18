<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Template_Category extends MageWorx_SeoXTemplates_Helper_Template
{
    const CATEGORY_META_TITLE       = 1;
    const CATEGORY_META_DESCRIPTION = 2;
    const CATEGORY_META_KEYWORDS    = 3;
    const CATEGORY_DESCRIPTION      = 4;
    const CATEGORY_SEO_NAME         = 5;

    /**
     *
     * @return array
     */
    public function getTypeArray()
    {
        return array(
            self::CATEGORY_SEO_NAME         => $this->__('Category SEO Name'),
            self::CATEGORY_META_TITLE       => $this->__('Category Meta Title'),
            self::CATEGORY_META_DESCRIPTION => $this->__('Category Meta Description'),
            self::CATEGORY_META_KEYWORDS    => $this->__('Category Meta Keywords'),
            self::CATEGORY_DESCRIPTION      => $this->__('Category Description')            
        );
    }

    /**
     * You can specify codes for new templates but it's not possible to change the codes for existing templates.
     * It's related to templates import process from previous versions of the extension.
     * Check the installation steps for more details
     * @return array
     */
    public function getTypeCodeArray()
    {
        return array(
            self::CATEGORY_SEO_NAME         => 'category_seo_name',
            self::CATEGORY_META_TITLE       => 'category_meta_title',
            self::CATEGORY_META_DESCRIPTION => 'category_meta_description',
            self::CATEGORY_META_KEYWORDS    => 'category_meta_keywords',
            self::CATEGORY_DESCRIPTION      => 'category_description'
        );
    }

    /**
     *
     * @return array
     */
    public function getAssignTypeArray()
    {
        return array(
            self::ASSIGN_ALL_ITEMS        => $this->__('All Categories'),
            self::ASSIGN_GROUP_ITEMS      => $this->__('By Category Branch'),
            self::ASSIGN_INDIVIDUAL_ITEMS => $this->__('Specific Categories'),
        );
    }
}
