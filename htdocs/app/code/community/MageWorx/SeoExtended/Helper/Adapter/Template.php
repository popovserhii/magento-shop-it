<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoExtended_Helper_Adapter_Template extends Mage_Core_Helper_Abstract
{
    public function isAvailableSeoTemplates()
    {
        if ((string)Mage::getConfig()->getModuleConfig('MageWorx_SeoTemplates')->active == 'true'){
            return true;
        }
    }

    public function getProductDynamicMeta($metaType, $product)
    {
        if(!$this->isAvailableSeoTemplates()){
            return false;
        }

        if (Mage::helper('seoextended/config')->getStatusDynamicMetaType($metaType) == 'off') {
            return false;
        }

        //Use only if clear original meta
        if (Mage::helper('seoextended/config')->getStatusDynamicMetaType($metaType) == 'on_for_empty') {
            if ($metaType == 'title') {
                if (trim($product->getMetaTitle())) {
                    return false;
                }
            }
            elseif ($metaType == 'description') {
                if (trim($product->getMetaDescription())) {
                    return false;
                }
            }
            elseif ($metaType == 'keywords') {
                if (trim($product->getMetaKeyword())) {
                    return false;
                }
            }
        }

        if ($metaType == 'title') {
            $metaTemplate = Mage::getModel('seotemplates/template')->loadTitle();
        }
        elseif ($metaType == 'description') {
            $metaTemplate = Mage::getModel('seotemplates/template')->loadMetaDescription();
        }
        elseif ($metaType == 'keywords') {
            $metaTemplate = Mage::getModel('seotemplates/template')->loadKeywords();
        }

        if (!empty($metaTemplate)) {
            $meta = $this->__compile($product, $metaTemplate, 'product');
        }
        return (!empty($meta)) ? trim($meta) : false;
    }

    public function getCategoryDynamicMeta($metaType, $category)
    {
        if(!$this->isAvailableSeoTemplates()){
            return false;
        }

        if (Mage::helper('seoextended/config')->getStatusDynamicMetaType($metaType) == 'off') {
            return false;
        }

        //Use only if clear original meta
        if (Mage::helper('seoextended/config')->getStatusDynamicMetaType($metaType) == 'on_for_empty') {
            if ($metaType == 'title') {
                if (trim($category->getMetaTitle())) {
                    return false;
                }
            }
            elseif ($metaType == 'description') {
                if (trim($category->getMetaDescription())) {
                    return false;
                }
            }
            elseif ($metaType == 'keywords') {
                if (trim($category->getMetaKeywords())) {
                    return false;
                }
            }
        }

        if ($metaType == 'title') {
            $metaTemplate = Mage::getModel('seotemplates/template')->loadCategoryTitle();
        }
        elseif ($metaType == 'description') {
            $metaTemplate = Mage::getModel('seotemplates/template')->loadCategoryMetaDescription();
        }
        elseif ($metaType == 'keywords') {
            $metaTemplate = Mage::getModel('seotemplates/template')->loadCategoryMetaKeywords();
        }

        if (!empty($metaTemplate)) {
            $meta = $this->__compile(Mage::registry('current_category'), $metaTemplate, 'category');
        }

        return (!empty($meta)) ? $meta : false;
    }

    protected function __compile($object, $template, $type = 'product')
    {
        if (!$object) {
            return '';
        }
        $template = Mage::getModel('seotemplates/catalog_' . $type . '_template_title')->getCompile($object, $template);
        return $template;
    }
}