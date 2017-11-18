<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Template_Product extends MageWorx_SeoXTemplates_Helper_Template
{
    const PRODUCT_NAME              = '1';
    const PRODUCT_URL_KEY           = '2';
    const PRODUCT_SHORT_DESCRIPTION = '3';
    const PRODUCT_DESCRIPTION       = '4';
    const PRODUCT_META_TITLE        = '5';
    const PRODUCT_META_DESCRIPTION  = '6';
    const PRODUCT_META_KEYWORDS     = '7';
    const PRODUCT_GALLERY           = '8';

    /**
     *
     * @return array
     */
    public function getTypeArray()
    {
        $types = array(
            self::PRODUCT_NAME              => $this->__('Product SEO Name'),
            self::PRODUCT_URL_KEY           => $this->__('Product URL Key'),
            self::PRODUCT_SHORT_DESCRIPTION => $this->__('Product Short Description'),
            self::PRODUCT_DESCRIPTION       => $this->__('Product Description'),
            self::PRODUCT_META_TITLE        => $this->__('Product Meta Title'),
            self::PRODUCT_META_DESCRIPTION  => $this->__('Product Meta Description'),
            self::PRODUCT_META_KEYWORDS     => $this->__('Product Meta Keywords'),
            self::PRODUCT_GALLERY           => $this->__('Product Gallery')
        );


        if(Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {
            unset($types[self::PRODUCT_URL_KEY]);
        }

        return $types;
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
            self::PRODUCT_NAME              => 'product_name',
            self::PRODUCT_URL_KEY           => 'product_url',
            self::PRODUCT_SHORT_DESCRIPTION => 'product_short_description',
            self::PRODUCT_DESCRIPTION       => 'product_description',
            self::PRODUCT_META_TITLE        => 'product_meta_title',
            self::PRODUCT_META_DESCRIPTION  => 'product_meta_description',
            self::PRODUCT_META_KEYWORDS     => 'product_meta_keywords',
            self::PRODUCT_GALLERY           => 'product_gallery'
        );
    }

    /**
     *
     * @return array
     */
    public function getAssignTypeArray()
    {
        return array(
            self::ASSIGN_ALL_ITEMS        => $this->__('All Products'),
            self::ASSIGN_GROUP_ITEMS      => $this->__('By Attribute Set'),
            self::ASSIGN_INDIVIDUAL_ITEMS => $this->__('Specific Products'),
        );
    }

    /**
     * Retrive note for grid with assigned products
     *
     * @return string Note
     */
    public function getNoteForGridTemplate()
    {
        return $this->__('Note: There is only one combination "Template Type – Store View – Product" available for the chosen Product.'
            . ' So Products assigned to different templates with the same conditions are hidden from Product Grid.');
    }
}
