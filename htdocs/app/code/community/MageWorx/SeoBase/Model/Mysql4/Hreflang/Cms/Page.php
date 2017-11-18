<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Hreflang_Cms_Page extends Mage_Core_Model_Mysql4_Abstract
{
    const XML_PATH_FILTER_PAGES = 'mageworx_seo/xsitemap/filter_pages';
    const XML_PATH_HOME_PAGE    = 'web/default/cms_home_page';

    protected function _construct()
    {
        $this->_init('cms/page', 'page_id');
        $this->_homePage      = Mage::getStoreConfig(self::XML_PATH_HOME_PAGE);
        $this->_baseStoreUrls = Mage::helper('mageworx_seobase/hreflang')->getBaseStoreUrls();
    }


    public function getAllCmsUrls($storeIds, $forStoreId, $itemId, $isHomePage = false)
    {
        array_push($storeIds, 0);

        $pages = array();
        $filterPages = Mage::getStoreConfig(self::XML_PATH_FILTER_PAGES, $forStoreId);
        $filterPages = explode(',', $filterPages);

        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('main_table' => $this->getMainTable()),
                array('main_table.page_id', 'main_table.identifier AS url', 'store_table.store_id AS store_id', 'cs.code AS code'))
            ->join(
                array('store_table' => $this->getTable('cms/page_store')), 'main_table.page_id=store_table.page_id',
                array()
            )
            ->join(
                array('cs' => $this->getTable('core/store')), 'cs.store_id=store_table.store_id', array()
            )
            ->where('main_table.identifier NOT IN(?)', $filterPages)
            ->where('main_table.exclude_from_sitemap=0')
            ->where('main_table.is_active=1')
            ->where('store_table.store_id IN(?)', $storeIds);

            if ($itemId) {
                if (Mage::helper('mageworx_seobase/hreflang')->getCmsPageRelationWay() == MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_ID) {
                    $select->where('main_table.page_id = ' . $itemId);
                }
                elseif (Mage::helper('mageworx_seobase/hreflang')->getCmsPageRelationWay() == MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_URLKEY) {
                    $origItemId = $itemId;
                    $itemId = Mage::getSingleton('cms/page')->load($itemId)->getIdentifier();
                    $select->where("main_table.identifier ='{$itemId}'");
                }
                elseif (Mage::helper('mageworx_seobase/hreflang')->getCmsPageRelationWay() == MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_IDENTIFIER) {
                    $origItemId = $itemId;
                    $itemId = Mage::getSingleton('cms/page')->load($itemId)->getMageworxHreflangIdentifier();

                    if ($isHomePage) {
                       $select->where('main_table.page_id = ?', $origItemId);
                    }
                    elseif (!$itemId) {
                        return array();
                    }
                    else {
                        $select->where("main_table.mageworx_hreflang_identifier ='{$itemId}'");
                    }
                }
            }

        $query = $read->query($select);
        $result = $query->fetchAll();

        $resultByPage = array();
        foreach ($result as $row) {
            $resultByPage[$row['page_id']][$row['store_id']] = $row;
        }

        foreach ($resultByPage as $result) {
            //For All stores
            if (!empty($result[0]) && $result[0]['store_id'] == 0) {
                //Home Page
                if ($isHomePage) {
                    foreach ($storeIds as $storeId) {
                        if ($storeId == 0) {
                            continue;
                        }
                        $alternateUrls[$storeId] = $this->_baseStoreUrls[$storeId];
                    }
                }
                //Other Page
                else {
                    foreach ($storeIds as $storeId) {
                        if ($storeId == 0) {
                            continue;
                        }

                        $alternateUrls[$storeId] = $this->_baseStoreUrls[$storeId] . $result[0]['url'];
                    }
                }
                $pages[$result[0]['page_id']] = array('url' => $result[0]['url'], 'alternateUrls' => $alternateUrls);
            }
            //For Individual Stores
            else {
                ///Home Page
                if ($isHomePage) {
                    foreach ($storeIds as $storeId) {

                        if ($storeId == 0) {
                            continue;
                        }
                        $alternateUrls[$storeId] = $this->_baseStoreUrls[$storeId];
                    }
                }
                ///Other Page
                else {
                    foreach ($result as $row) {
                        $alternateUrls[$row['store_id']] = $this->_baseStoreUrls[$row['store_id']] . $row['url'];
                    }
                }

                if (!empty($row)) {

                    if (Mage::helper('mageworx_seobase/hreflang')->getCmsPageRelationWay() == MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_URLKEY ||
                       Mage::helper('mageworx_seobase/hreflang')->getCmsPageRelationWay() == MageWorx_SeoBase_Helper_Hreflang::CMS_RELATION_BY_IDENTIFIER
                    ) {
                        if (!empty($pages[$origItemId]['url']['alternateUrls'])) {
                            $pages[$origItemId]['alternateUrls'] = $pages[$origItemId]['alternateUrls'] + $alternateUrls;
                        } else {
                            $pages[$origItemId] = array('url' => $row['url'], 'alternateUrls' => $alternateUrls);
                        }
                    } else {
                        $pages[$row['page_id']] = array('url' => $row['url'], 'alternateUrls' => $alternateUrls);
                    }
                }
            }
        }

        return $pages;
    }
}