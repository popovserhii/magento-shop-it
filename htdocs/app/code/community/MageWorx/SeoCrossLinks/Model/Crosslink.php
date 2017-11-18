<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Crosslink extends Mage_Core_Model_Abstract
{
    const TARGET_LINK_SELF  = '_self';

    const TARGET_LINK_BLANK = '_blank';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seocrosslinks/crosslink');
        $this->setIdFieldName('crosslink_id');
    }

    /**
     * Retrive Target Links Values
     *
     * @return array
     */
    public function getTargetLinkArray()
    {
        return array(0 => self::TARGET_LINK_SELF, 1 => self::TARGET_LINK_BLANK);
    }

    /**
     * Retrive Target Links Descriptions
     *
     * @return array
     */
    public function getTargetLinkDescriptionArray()
    {
        return array(
            0 => Mage::helper('mageworx_seocrosslinks')->__('current window') . ' ' . '(' . self::TARGET_LINK_SELF . ')',
            1 => Mage::helper('mageworx_seocrosslinks')->__('new window'). ' ' . '(' . self::TARGET_LINK_BLANK . ')'
        );
    }

    /**
     * Retrive Target Links Value
     *
     * @param int $num
     * @return string
     */
    public function getTargetLinkValue($num = 0)
    {
        $targets = $this->getTargetLinkArray();
        return !empty($targets[$num]) ? $targets[$num] : self::TARGET_LINK_SELF;
    }

    /**
     * Replace keywords to links in html
     *
     * @param string $entity
     * @param string $html
     * @param int $maxReplaceCount
     * @param string $ignoreProductSku
     * @param int $ignoreCategoryId
     * @return string
     */
    public function replace($entity, $html, $maxReplaceCount, $ignoreProductSku = null, $ignoreCategoryId = null)
    {
        $collection = $this->getCollection();

        switch($entity){
            case 'product':
                $collection->addInProductFilter();
                break;
            case 'category':
                $collection->addInCategoryFilter();
                break;
            case 'aw_blog':
                $collection->addInBlogFilter();
                break;
            case 'cms_page':
                $collection->addInCmsPageFilter();
                break;
            default:
                return false;
        }

        $collection->addStoreFilter(Mage::app()->getStore());
        $collection->addEnabledFilter();
        $collection->setOrder('priority', 'DESC');

        $replacer = Mage::getSingleton('mageworx_seocrosslinks/replacer');
        return $replacer->replace($collection, $html, $maxReplaceCount, $ignoreProductSku, $ignoreCategoryId);
    }

    /**
     * @return \MageWorx_SeoCrossLinks_Model_Crosslink
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->getData('ref_static_url')) {
            $this->setData('reference', 'ref_static_url');
        } elseif ($this->getData('ref_product_sku')) {
            $this->setData('reference', 'ref_product_sku');
        } elseif ($this->getData('ref_category_id')) {
            $this->setData('reference', 'ref_category_id');
        }
        return $this;
    }

    /**
     *
     * @return \MageWorx_SeoCrossLinks_Model_Crosslink
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        switch($this->getData('reference')) {
            case 'ref_static_url':
                $this->setData('ref_product_sku', null);
                $this->setData('ref_category_id', null);
                break;
            case 'ref_product_sku':
                $this->setData('ref_static_url',  null);
                $this->setData('ref_category_id', null);
                break;
            case 'ref_category_id':
                $this->setData('ref_static_url',  null);
                $this->setData('ref_product_sku', null);
                break;
        }
        return $this;
    }
}
