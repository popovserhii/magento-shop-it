<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Block_Adminhtml_System_Config_Frontend_Hreflang_Selftest extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        if (Mage::helper('mageworx_seobase/hreflang')->getAlternateScope() == 'website') {
            return $this->_getWebsiteTableHtml($this->_getSource());
        }

        return $this->_getGlobalTableHtml($this->_getSource());
    }

    protected function _getSource()
    {
        $data = array();
        $allStores = Mage::app()->getStores();

        foreach ($allStores as $store) {

            if (!$store->getIsActive()) continue;

            $duplicateProduct  = false;
            $duplicateCategory = false;
            $duplicateCms      = false;

            if (empty($data[$store->getWebsiteId()])) {
                $data[$store->getWebsiteId()] = array();
            }

            $alternateProductCodes = Mage::helper('mageworx_seobase/hreflang')->getAlternateRawCodes('product',
                $store->getStoreId());

            if (!empty($alternateProductCodes[$store->getStoreId()])) {
                $duplicateProduct = $this->_markDuplicateData($data, 'product_hreflang_code', $store->getWebsiteId(),
                    $alternateProductCodes[$store->getStoreId()]);

                if ($duplicateProduct) {
                    $store->setData('product_hreflang_code_duplicate', '1');
                }
                $store->setData('product_hreflang_code', $alternateProductCodes[$store->getStoreId()]);
            }

            $alternateCategoryCodes = Mage::helper('mageworx_seobase/hreflang')->getAlternateRawCodes('category',
                $store->getStoreId());

            if (!empty($alternateCategoryCodes[$store->getStoreId()])) {
                $duplicateCategory = $this->_markDuplicateData($data, 'category_hreflang_code', $store->getWebsiteId(),
                    $alternateCategoryCodes[$store->getStoreId()]);

                if ($duplicateCategory) {
                    $store->setData('category_hreflang_code_duplicate', '1');
                }
                $store->setData('category_hreflang_code', $alternateCategoryCodes[$store->getStoreId()]);
            }

            $alternateCmsCodes = Mage::helper('mageworx_seobase/hreflang')->getAlternateRawCodes('cms', $store->getStoreId());

            if (!empty($alternateCmsCodes[$store->getStoreId()])) {
                $duplicateCms = $this->_markDuplicateData($data, 'cms_hreflang_code', $store->getWebsiteId(),
                    $alternateCmsCodes[$store->getStoreId()]);

                if ($duplicateCms) {
                    $store->setData('cms_hreflang_code_duplicate', '1');
                }
                $store->setData('cms_hreflang_code', $alternateCmsCodes[$store->getStoreId()]);
            }

            $data[$store->getWebsiteId()]['website_name']       = $store->getWebsite()->getName();
            $data[$store->getWebsiteId()][$store->getStoreId()] = $store->getData();
        }

        return $data;
    }

    protected function _markDuplicateData(&$data, $type, $websiteId, $code)
    {
        $duplicateFlag = false;
        foreach ($data as $webId => $website) {
            if (Mage::helper('mageworx_seobase/hreflang')->getAlternateScope() == 'website' && $webId != $websiteId) {
                continue;
            }

            if (is_array($website) && !empty($website)) {
                foreach ($website as $storeId => $store) {
                    if (!empty($store[$type]) && $store[$type] == $code) {
                        $data[$webId][$storeId][$type . '_duplicate'] = 1;
                        $duplicateFlag                                = true;
                    }
                }
            }
        }

        return $duplicateFlag;
    }

    protected function _getGlobalTableHtml($data)
    {
        $html = '';
        $html .= '<style type="text/css">
                        .tg  {border-collapse:collapse;border-spacing:0;}
                        .tg td{padding:5px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                        .tg th{padding:7px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                    </style>
                   ';

        $html .= "<table class='tg'>
                      <tr>
                        <th colspan='2'>Store (code/ID)</th>
                        <th colspan='3'>Hreflang Code</th>
                      </tr>
                      <tr>
                        <th colspan='2'></th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>CMS Page</th>
                      </tr>";

        foreach ($data as $websiteId => $websiteData) {

            if (count($websiteData) < 2) {
                continue;
            }

            $websiteHint = (stripos($websiteData['website_name'], 'website') === false) ? ' Website' : '';
            $html .= "<tr>
                        <th colspan='5'>" . $websiteData['website_name'] . "{$websiteHint}</th>
                      </tr>
                      ";

            unset($websiteData['website_name']);

            foreach ($websiteData as $storeData) {

                $productStoreHreflang  = empty($storeData['product_hreflang_code']) ? '-' : $storeData['product_hreflang_code'];
                $productDuplicateColor = empty($storeData['product_hreflang_code_duplicate']) ? '' : ' color=red';

                $categoryStoreHreflang  = empty($storeData['category_hreflang_code']) ? '-' : $storeData['category_hreflang_code'];
                $categoryDuplicateColor = empty($storeData['category_hreflang_code_duplicate']) ? '' : ' color=red';

                $cmsStoreHreflang  = empty($storeData['cms_hreflang_code']) ? '-' : $storeData['cms_hreflang_code'];
                $cmsDuplicateColor = empty($storeData['cms_hreflang_code_duplicate']) ? '' : ' color=red';

                $html .= "<tr>
                            <td colspan='2'>" . $storeData['name'] . "</td>
                            <td><font{$productDuplicateColor}>" . $productStoreHreflang . "</font></td>
                            <td><font{$categoryDuplicateColor}>" . $categoryStoreHreflang . "</font></td>
                            <td><font{$cmsDuplicateColor}>" . $cmsStoreHreflang . "</font></td>
                          </tr>";
            }
        }

        $html .= '</table><br>';

        return $html;
    }

    protected function _getWebsiteTableHtml($data)
    {
        $html = '';
        $html .= '<style type="text/css">
                        .tg  {border-collapse:collapse;border-spacing:0;}
                        .tg td{padding:5px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                        .tg th{padding:7px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
                    </style>
                   ';

        foreach ($data as $websiteId => $websiteData) {

            if (count($websiteData) < 2) {
                continue;
            }

            $websiteHint = (stripos($websiteData['website_name'], 'website') === false) ? 'Website Name: ' : '';
            $html .= "<table class='tg'>
                      <tr>
                        <th colspan='5'>{$websiteHint}" . $websiteData['website_name'] . "</th>
                      </tr>
                      <tr>
                        <th colspan='2'>Store (code/ID)</th>
                        <th colspan='3'>Hreflang Code</th>
                      </tr>
                      <tr>
                        <th colspan='2'></th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>CMS Page</th>
                      </tr>";

            unset($websiteData['website_name']);

            foreach ($websiteData as $storeData) {

                $productStoreHreflang  = empty($storeData['product_hreflang_code']) ? '-' : $storeData['product_hreflang_code'];
                $productDuplicateColor = empty($storeData['product_hreflang_code_duplicate']) ? '' : ' color=red';

                $categoryStoreHreflang  = empty($storeData['category_hreflang_code']) ? '-' : $storeData['category_hreflang_code'];
                $categoryDuplicateColor = empty($storeData['category_hreflang_code_duplicate']) ? '' : ' color=red';

                $cmsStoreHreflang  = empty($storeData['cms_hreflang_code']) ? '-' : $storeData['cms_hreflang_code'];
                $cmsDuplicateColor = empty($storeData['cms_hreflang_code_duplicate']) ? '' : ' color=red';

                $html .= "<tr>
                            <td colspan='2'><b>" . $storeData['name'] . "</b><br>(" . $storeData['code'] . " / " . $storeData['store_id'] . ")</td>
                            <td><font{$productDuplicateColor}>" . $productStoreHreflang . "</font></td>
                            <td><font{$categoryDuplicateColor}>" . $categoryStoreHreflang . "</font></td>
                            <td><font{$cmsDuplicateColor}>" . $cmsStoreHreflang . "</font></td>
                          </tr>";
            }

            $html .= '</table><br>';
        }

        return $html;
    }
}