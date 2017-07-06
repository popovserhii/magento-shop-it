<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Mysql4_Cms_Page extends Mage_Core_Model_Mysql4_Abstract
{
    const XML_PATH_FILTER_PAGES = 'mageworx_seo/xsitemap/filter_pages';

    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
    }

    public function getCollection($storeId)
    {
        $pages = array();

        $filterPages = Mage::getStoreConfig(self::XML_PATH_FILTER_PAGES, $storeId);
        $filterPages = explode(',', $filterPages);

        $read   = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName(), 'identifier AS url'))
            ->join(
                array('store_table' => $this->getTable('cms/page_store')), 'main_table.page_id=store_table.page_id',
                array()
            )
            ->where('main_table.identifier NOT IN(?)', $filterPages)
            ->where('main_table.exclude_from_sitemap=0')
            ->where('main_table.is_active=1')
            ->where('store_table.store_id IN(?)', array(0, $storeId));

        $query = $read->query($select);
        while ($row   = $query->fetch()) {
            $page                  = $this->_prepareObject($row);
            $pages[$page->getId()] = $page;
        }

        return $pages;
    }

    protected function _prepareObject(array $data)
    {
        $object = new Varien_Object();
        $object->setId($data[$this->getIdFieldName()]);
        $object->setUrl($data['url']);

        return $object;
    }

}