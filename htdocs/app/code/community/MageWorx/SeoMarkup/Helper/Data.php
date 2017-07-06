<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Data extends Mage_Core_Helper_Abstract
{

    const IN_STOCK     = 'http://schema.org/InStock';
    const OUT_OF_STOCK = 'http://schema.org/OutOfStock';
    const OFFER        = 'http://schema.org/Offer';

    protected $_attributeValues = array();

    protected $_conditionValue;

    protected $_productCanonicalUrl;

    protected $_ratingData;

    protected $_deliveryMethodsList;

    protected $_paymentMethodList;

    protected $_categoryName;

    public function getCurrentFullActionName()
    {
        return Mage::helper('mageworx_seoall/request')->getCurrentFullActionName();
    }

    public function isHomePage()
    {
        return 'cms_index_index' == $this->getCurrentFullActionName();
    }

    public function isCmsPage()
    {
        return 'cms_page_view' == $this->getCurrentFullActionName();
    }

    public function isProductPage()
    {
        return 'catalog_product_view' == $this->getCurrentFullActionName();
    }

    public function isCategoryPage()
    {
        return 'catalog_category_view' == $this->getCurrentFullActionName();
    }

    public function getStoreBaseUrl()
    {
        $url = Mage::app()->getStore()->getUrl();
        $cropUrl = (strpos($url, "?")) ? substr($url, 0, strpos($url, "?")) : $url;
        return rtrim($cropUrl, '/') . '/';
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeCode
     * @return mixed
     */
    public function getAttributeValueByCode($product, $attributeCode)
    {
        if (array_key_exists($attributeCode, $this->_attributeValues)) {
            return $this->_attributeValues[$attributeCode];
        }
        $finalValue = Mage::helper('mageworx_seoall/attribute')->getAttributeValueByCode($product, $attributeCode);
        $this->_attributeValues[$attributeCode] = $finalValue;

        return $this->_attributeValues[$attributeCode];
    }

    /**
     * @todo Retrive product canonical URL from SeoBase or Magento Canonical URL.
     * @return string
     */
    public function getProductCanonicalUrl($product)
    {
        if (!empty($this->productCanonicalUrl)) {
            return $this->productCanonicalUrl;
        }
        $this->productCanonicalUrl = $product->getUrlModel()->getUrl($product, array('_ignore_category' => true));
        return $this->productCanonicalUrl;
    }

    public function getConditionValue($product)
    {
        if (!is_null($this->_conditionValue)) {
            return $this->_conditionValue;
        }

        $attributeCode      = Mage::helper('mageworx_seomarkup/config')->getConditionAttributeCode();
        $conditionByDefault = Mage::helper('mageworx_seomarkup/config')->getConditionDefaultValue();

        if ($attributeCode) {
            $conditionValue = Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            switch ($conditionValue) {
                case Mage::helper('mageworx_seomarkup/config')->getConditionValueForNew():
                    $conditionValue = "NewCondition";
                    break;
                case Mage::helper('mageworx_seomarkup/config')->getConditionValueForUsed():
                    $conditionValue = "UsedCondition";
                    break;
                 case Mage::helper('mageworx_seomarkup/config')->getConditionValueForRefurbished():
                    $conditionValue  = "RefurbishedCondition";
                    break;
                case Mage::helper('mageworx_seomarkup/config')->getConditionValueForDamaged():
                    $conditionValue = "DamagedCondition";
                    break;
                default:
                    if ($conditionByDefault) {
                        $conditionValue = $conditionByDefault;
                    }
                    break;
            }
        } elseif ($conditionByDefault) {
             $conditionValue = $conditionByDefault;
        }

        $conditionValue = !empty($conditionValue) ? $conditionValue : false;
        $this->_conditionValue = $conditionValue;

        return $this->_conditionValue;
    }

    public function getAggregateRatingData($product, $useMagentoBestRating = true)
    {
        if (!is_null($this->_ratingData)) {
            return $this->_ratingData;
        }

        $currentStoreId = Mage::app()->getStore()->getId();
        $reviewModel    = Mage::getModel('review/review_summary')->setStoreId($currentStoreId)->load($product->getId());

        if (!is_object($reviewModel)) {
            $this->_ratingData = false;
            return $this->_ratingData;
        }

        if (!$reviewModel['reviews_count']) {
            $this->_ratingData = false;
            return $this->_ratingData;
        }

        $data = array();
        $data['@type']   = 'AggregateRating';

        if (Mage::helper('mageworx_seomarkup/config')->getBestRating() && !$useMagentoBestRating) {
            $bestRating  = Mage::helper('mageworx_seomarkup/config')->getBestRating();
            $rating      = round(($reviewModel['rating_summary'] / (100 / $bestRating)), 1);
        } else {
            $bestRating = 100;
            $rating     = $reviewModel['rating_summary'];
        }

        $data['ratingValue'] = $rating;
        $data['reviewCount'] = $reviewModel['reviews_count'];
        $data['bestRating']  = $bestRating;
        $data['worstRating'] = 0;

        $this->_ratingData = $data;

        return $this->_ratingData;
	}

    /**
     * @param array $reviewData
     * @return bool
     */
    public function isValidReviewData($reviewData)
    {
        if (empty($reviewData)) {
            return false;
        }

        if (empty($reviewData['reviewCount'])) {
            return false;
        }

        if (empty($reviewData['ratingValue'])) {
            return false;
        }

        return true;
    }

    public function getDescriptionValue($product)
    {
        $attributeCode = Mage::helper('mageworx_seomarkup/config')->getDescriptionAttributeCode();
        if ($attributeCode) {
            $description = Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            return htmlspecialchars(strip_tags($description));
        }
        return htmlspecialchars(strip_tags($product->getShortDescription()));
    }

    public function getColorValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetColorEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getColorAttributeCode();
            if ($attributeCode) {
                return Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    public function getBrandValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetBrandEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getBrandAttributeCode();
            if ($attributeCode) {
                return Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    public function getManufacturerValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetManufacturerEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getManufacturerAttributeCode();
            if ($attributeCode) {
                return Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    public function getModelValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetModelEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getModelAttributeCode();
            if ($attributeCode) {
                return Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            }
        }
        return null;
    }

    public function getGtinData($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetGtinEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getGtinAttributeCode();
            if (!$attributeCode) {
                return null;
            }

            $gtinValue = Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            if (preg_match('/^[0-9]+$/', $gtinValue)) {

                if (strlen($gtinValue) == 8) {
                    $gtinType = 'gtin8';
                } elseif (strlen($gtinValue) == 12) {
                    $gtinValue = '0' . $gtinValue;
                    $gtinType = 'gtin13';
                } elseif (strlen($gtinValue) == 13) {
                    $gtinType = 'gtin13';
                } elseif (strlen($gtinValue) == 14) {
                    $gtinType = 'gtin14';
                }
            }
        }

        return !empty($gtinType) ? array('gtinType' => $gtinType, 'gtinValue' => $gtinValue) : null;
    }

    public function getSkuValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetSkuEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getSkuAttributeCode();
            if ($attributeCode) {
                $sku = Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
            } else {
                $sku = $product->getSku();
            }
            return $sku;
        }
        return null;
    }

    public function getHeightValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetHeightEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getHeightAttributeCode();

            if ($attributeCode) {
                return $this->_getDimension($product, $attributeCode);
            }
        }
        return null;
    }

    public function getWidthValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetWidthEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getWidthAttributeCode();

            if ($attributeCode) {
                return $this->_getDimension($product, $attributeCode);
            }
        }
        return null;
    }

    public function getDepthValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetDepthEnabled()) {
            $attributeCode = Mage::helper('mageworx_seomarkup/config')->getDepthAttributeCode();

            if ($attributeCode) {
                return $this->_getDimension($product, $attributeCode);
            }
        }
        return null;
    }

    public function getWeightValue($product)
    {
        if (Mage::helper('mageworx_seomarkup/config')->isRichsnippetWeightEnabled()) {
            $weightValue = $product->getWeight();
            if($weightValue){
                $weightUnit  = Mage::helper('mageworx_seomarkup/config')->getRichsnippetWeightUnit();
                return $weightValue . ' ' . $weightUnit;
            }
        }
        return null;
    }

    public function getCustomPropertyValue($product, $propertyName)
    {
        $customProperty = Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $propertyName);
        return $customProperty ? $customProperty : null;
    }

    public function getCategoryValue($product)
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isRichsnippetCategoryEnabled()) {
            return null;
        }

        if (!is_null($this->_categoryName)) {
            return $this->_categoryName;
        }

        $categories = $product->getCategoryCollection()->exportToArray();
        $currentCategory = Mage::registry('current_category');
        $useDeepestCategory = Mage::helper('mageworx_seomarkup/config')->isRichsnippetCategoryDeepest();

        if (is_object($currentCategory)) {
            if (count($categories) > 1) {
                if ($useDeepestCategory) {
                    $currentId = $currentCategory->getId();
                    $currentLevel = $currentCategory->getLevel();
                    if (is_numeric($currentLevel)) {
                        foreach ($categories as $category) {
                            if ($category['level'] > $currentLevel) {
                                $currentId = $category['entity_id'];
                                $currentLevel = $category['level'];
                            }
                        }
                        if ($currentId != $currentCategory->getId()) {
                            $categoryName = $this->_getCategoryNameById($currentId);
                        }
                    }
                }
            }
            if (empty($categoryName)) {
                $this->_categoryName = $currentCategory->getName();
            }
        } else {
            if ($useDeepestCategory) {
                if (count($categories) > 0) {
                    $currentId = 0;
                    $currentLevel = 0;
                    if (is_numeric($currentLevel)) {
                        foreach ($categories as $category) {
                            if ($category['level'] >= $currentLevel) {
                                $currentId = $category['entity_id'];
                                $currentLevel = $category['level'];
                            }
                        }
                        if ($currentId) {
                            $this->_categoryName = $this->_getCategoryNameById($currentId);
                        }
                    }
                } else {
                    $this->_categoryName = false;
                }
            } else {
                $this->_categoryName = false;
            }
        }

        return $this->_categoryName;
    }

    public function getPaymentMethods()
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isRichsnippetPaymentEnabled()) {
            return false;
        }

        if (!is_null($this->_paymentMethodList)) {
            return $this->_paymentMethodList;
        }

        $paymentMethodList = Mage::helper('mageworx_seomarkup/config')->getPaymentMethodList();
        if (!empty($paymentMethodList)) {
            $this->_paymentMethodList = $paymentMethodList;
            return $this->_paymentMethodList;
        }

        $_paymentMethods = array(
            "byBankTransferInAdvance" => "http://purl.org/goodrelations/v1#ByBankTransferInAdvance",
            "byInvoice"               => "http://purl.org/goodrelations/v1#ByInvoice",
            "cash"                    => "http://purl.org/goodrelations/v1#Cash",
            "checkinadvance"          => "http://purl.org/goodrelations/v1#CheckInAdvance",
            "cod"                     => "http://purl.org/goodrelations/v1#COD",
            "directdebit"             => "http://purl.org/goodrelations/v1#DirectDebit",
            "googleCheckout"          => "http://purl.org/goodrelations/v1#GoogleCheckout",
            "paypal"                  => "http://purl.org/goodrelations/v1#PayPal",
            "AE"                      => "http://purl.org/goodrelations/v1#AmericanExpress",
            "DI"                      => "http://purl.org/goodrelations/v1#Discover",
            "JCB"                     => "http://purl.org/goodrelations/v1#JCB",
            "MC"                      => "http://purl.org/goodrelations/v1#MasterCard",
            "VI"                      => "http://purl.org/goodrelations/v1#VISA",
        );

        $data = array();
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        foreach ($payments as $paymentCode => $paymentModel) {

            if ($paymentModel->canUseCheckout() == 1) {
                if ($paymentCode) {
                    switch ($paymentCode) {
                        case "ccsave":
                            $ccsave = $this->_getCcAvailableTypes($paymentModel);
                            foreach ($ccsave as $cc) {
                                if (in_array($cc, $_paymentMethods)) {
                                    $data[] = $_paymentMethods[$cc];
                                }
                            }
                            break;
                        case "checkmo":
                            $data[] = $_paymentMethods['checkinadvance'];
                            $data[] = $_paymentMethods['cash'];
                            break;
                        case "purchaseorder":
                            $data[] = $_paymentMethods['byInvoice'];
                            break;
                        case "banktransfer":
                            $data[] = $_paymentMethods['byBankTransferInAdvance'];
                            break;
                        case "cashondelivery":
                            $data[] = $_paymentMethods['cod'];
                            break;

                        case "paypaluk_express":
                        case "paypaluk_direct":
                        case "paypal_direct":
                        case "payflow_link":
                        case "verisign":
                        case "payflow_advanced":
                        case "paypal_standard":
                        case "paypal_express":
                            $data[] = $_paymentMethods['paypal'];
                            break;
                        case "free":
                        case "authorizenet":
                            $sCC            = Mage::getStoreConfig('payment/authorizenet/cctypes');
                            $aCC            = explode(',', $sCC);
                            foreach ($aCC as $cc) {
                                if (in_array($cc, array('AE', 'VI', 'MC', 'DI', 'JCB'))) {
                                    $data[] = $_paymentMethods[$cc];
                                }
                            }
                            break;
                        case "authorizenet_directpost":
                            $sCC = Mage::getStoreConfig('payment/authorizenet_directpost/cctypes');
                            $aCC = explode(',', $sCC);
                            foreach ($aCC as $cc) {
                                if (in_array($cc, array('AE', 'VI', 'MC', 'DI', 'JCB'))) {
                                    $data[] = $_paymentMethods[$cc];
                                }
                            }
                            break;
                        default :
                            break;
                    }
                }
            }
        }

        return $data;
    }

    public function getDeliveryMethods()
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isRichsnippetDeliveryEnabled()) {
            return false;
        }

        if (!is_null($this->_deliveryMethodsList)) {
            return $this->_deliveryMethodsList;
        }

        $deliveryMethodsList = Mage::helper('mageworx_seomarkup/config')->getDeliveryMethodList();
        if (!empty($deliveryMethodsList)) {
            $this->_deliveryMethodsList = $deliveryMethodsList;
            return $this->_deliveryMethodsList;
        }

        $_deliveryMethods = array(
            "dhl"               => "http://purl.org/goodrelations/v1#DHL",
            "ups"               => "http://purl.org/goodrelations/v1#UPS",
            "mail"              => "http://purl.org/goodrelations/v1#DeliveryModeMail",
            "fedex"             => "http://purl.org/goodrelations/v1#FederalExpress",
            "directdownload"    => "http://purl.org/goodrelations/v1#DeliveryModeDirectDownload",
            "pickup"            => "http://purl.org/goodrelations/v1#DeliveryModePickUp",
            "vendorfleet"       => "http://purl.org/goodrelations/v1#DeliveryModeOwnFleet",
            "freight"           => "http://purl.org/goodrelations/v1#DeliveryModeFreight"
        );

        $data = array();

        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();

        foreach ($methods as $code => $method)
        {
            switch ($code) {
                case "dhlint":
                    $data['dhl'] = $_deliveryMethods['dhl'];
                    break;
                case "ups":
                    $data['dhl'] = $_deliveryMethods['ups'];
                    break;
                case "fedex":
                    $data['dhl'] = $_deliveryMethods['fedex'];
                    break;
                case "usps":
                case "tablerate":
                case "freeshipping":
                case "flatrate":
                default :
                    $data['dhl'] = $_deliveryMethods['freight'];
            }
        }

        $this->_deliveryMethodsList = array_values($data);
        return $this->_deliveryMethodsList;
    }

    public function getProductThumbnail($product)
    {
		return Mage::helper('catalog/image')->init($product, 'small_image')->resize(100);
	}

	public function getProductImage($product)
    {
		return Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
	}

    public function getAvailability($product)
    {
        /**
         * getIsSalable() method load all associated product models for composite product type
         */
        if (method_exists(Mage::helper('catalog/product'), 'getSkipSaleableCheck')) {
            if(Mage::helper('catalog/product')->getSkipSaleableCheck()){
                return self::IN_STOCK;
            }
        }

        if ($product->getData('is_salable')) {
            return self::IN_STOCK;
        }

        return self::OUT_OF_STOCK;
    }

    protected function _getDimension($product, $attributeCode)
    {
        $dimentionValue = Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);

        if ($dimentionValue) {
            $unit = Mage::helper('mageworx_seomarkup/config')->getRichsnippetDimensionsUnit();
            if (is_numeric($dimentionValue) && $unit) {
                return $dimentionValue . ' ' . $unit;
            } elseif (preg_match('/^([0-9]+[\s]+[a-zA-Z]+)$/', $dimentionValue)) {
                return $dimentionValue;
            }
            return null;
        }
        return null;
    }

    protected function _getCategoryNameById($id)
    {
        if ($id) {
            $storeId = Mage::app()->getStore()->getStoreId();
            $resourceModel = Mage::getResourceModel('catalog/category');
            if (is_callable(array($resourceModel, 'getAttributeRawValue'))) {
                return $resourceModel->getAttributeRawValue($id, 'name', $storeId);
            }

            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->addAttributeToFilter('entity_id', $id);
            $collection->addAttributeToSelect('name');
            $category = $collection->getFirstItem();
            return $category->getAttributeText('name');
        }
        return false;
    }

    protected function _getCcAvailableTypes($method)
    {
        $types = Mage::getSingleton('payment/config')->getCcTypes();
        if ($method) {
            $availableTypes = $method->getConfigData('cctypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;
    }

    public function getEventStartDateValue($product)
    {
        $attributeCode = Mage::helper('mageworx_seomarkup/config')->getEventStartDateCode();

        if ($attributeCode) {
            return Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
        }
        return null;
    }

    public function getEventAddressLocalityValue($product)
    {
        $attributeCode = Mage::helper('mageworx_seomarkup/config')->getEventAddressLocalityCode();
        if ($attributeCode) {
            return Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
        }
        return null;
    }

    public function getEventAddressStreetValue($product)
    {
        $attributeCode = Mage::helper('mageworx_seomarkup/config')->getEventAddressStreetCode();
        if ($attributeCode) {
            return Mage::helper('mageworx_seomarkup')->getAttributeValueByCode($product, $attributeCode);
        }
        return null;
    }

    public function getCurrentEntityNameList()
    {
        if ($this->isProductPage()) {
            $product = Mage::registry('current_product');
            if(is_object($product) && $product->getName()){
                return array(Mage::helper('core')->escapeHtml($product->getName()));
            }
        } elseif ($this->isCategoryPage()) {
            $category = Mage::registry('current_category');
            if(is_object($category) && $category->getName()){
                return array(Mage::helper('core')->escapeHtml($category->getName()));
            }
        } elseif ($this->isCmsPage()) {
            $cmsPage = Mage::getSingleton('cms/page');

            if(is_object($cmsPage)){
                return array(Mage::helper('core')->escapeHtml($cmsPage->getTitle()));
            }
        }
        return false;
    }

}