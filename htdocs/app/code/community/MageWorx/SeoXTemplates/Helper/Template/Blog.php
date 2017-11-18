<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Template_Blog extends MageWorx_SeoXTemplates_Helper_Template
{
    const BLOG_TITLE        = 1;
    const BLOG_META_DESCRIPTION  = 2;
    const BLOG_META_KEYWORDS     = 3;

    /**
     *
     * @return array
     */
    public function getTypeArray()
    {
        return array(
            self::BLOG_TITLE        => $this->__('Blog Post Title'),
            self::BLOG_META_DESCRIPTION  => $this->__('Blog Post Meta Description'),
            self::BLOG_META_KEYWORDS     => $this->__('Blog Post Meta Keywords'),
        );
    }

    /**
     *
     * @return array
     */
    public function getTypeCodeArray()
    {
        return array(
            self::BLOG_TITLE        => 'blog_title',
            self::BLOG_META_DESCRIPTION  => 'blog_meta_description',
            self::BLOG_META_KEYWORDS     => 'blog_meta_keywords',
        );
    }

    /**
     *
     * @return array
     */
    public function getAssignTypeArray()
    {
        return array(
            self::ASSIGN_ALL_ITEMS        => $this->__('All Blog Posts'),
            self::ASSIGN_INDIVIDUAL_ITEMS => $this->__('Specific Blog Posts'),
        );
    }

    public function isEnabled()
    {
        return ((string)Mage::getConfig()->getModuleConfig('AW_Blog')->active == 'true');
    }

    public function getBlogCategoryOptionArray()
    {
        $options = array();
        foreach(Mage::getModel('blog/cat')->getCollection() as $cat) {
            $options[$cat['cat_id']] = $this->__($cat['title']);
        }
        return $options;
    }

    /**
     * Retrive note for grid with assigned blog posts
     *
     * @return string Note
     */
    public function getNoteForGridTemplate()
    {
        return $this->__('Note: There is only one combination "Template Type – Blog Post" available for the chosen Blog Post.'
            . ' So Blog Posts assigned to different templates are hidden from Blog Post Grid.');
    }
}
