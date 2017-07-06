<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Json_Event extends Mage_Core_Helper_Abstract
{
    protected $_product;

    public function getJsonEventData($product)
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isEventRichsnippetEnabled()) {
            return false;
        }

        if (!in_array($product->getAttributeSetId(), Mage::helper('mageworx_seomarkup/config')->getEventAttributeSets())) {
            return false;
        }

        $this->_product = $product;
        $data = array();
        $data['@context']    = 'http://schema.org';
        $data['@type']       = 'Event';
        $data['name']        = $product->getName();

        $startDate = Mage::helper('mageworx_seomarkup')->getEventStartDateValue($this->_product);
        if (!$startDate) {
            return false;
        }

        $data['startDate']   = $startDate;
        $data['description'] = Mage::helper('mageworx_seomarkup')->getDescriptionValue($this->_product);
        $data['image']       = Mage::helper('mageworx_seomarkup')->getProductImage($this->_product);
        $data['offers']      = $this->_getOfferData();

        if (!$data['offers']['price']) {
            unset($data['offers']);
        }

        $locationData = $this->_getLocationData();

        if (!$locationData) {
            return false;
        }

        $data['location'] = $locationData;

        if (Mage::helper('mageworx_seomarkup/config')->isRatingEnabled()) {

            $aggregateRatingData = Mage::helper('mageworx_seomarkup')->getAggregateRatingData($this->_product, false);

            if (Mage::helper('mageworx_seomarkup')->isValidReviewData($aggregateRatingData)) {
                $data['aggregateRating'] = $aggregateRatingData;
            }
        }

        return $data;
    }

    protected function _getLocationData()
    {
        $name            = Mage::helper('mageworx_seomarkup/config')->getEventLocationName();
        if (!$name) {
            return false;
        }

        $addressLocality = Mage::helper('mageworx_seomarkup')->getEventAddressLocalityValue($this->_product);
        if (!$addressLocality) {
            return false;
        }

        $addressStreet   = Mage::helper('mageworx_seomarkup')->getEventAddressStreetValue($this->_product);

        $data = array();
        $data['@type'] = 'Place';
        $data['name']  = $name;

        if ($addressLocality) {
            $address = array();
            $address["@type"]           = "PostalAddress";
            $address["addressLocality"] = $addressLocality;
            if ($addressStreet) {
                $address["addressStreet"] = $addressStreet;
            }

            $data['address'] = $address;
        }

        return $data;
    }

    protected function _getOfferData()
    {
        $data   = array();
        $prices = Mage::helper('mageworx_seomarkup/price')->getPricesByProductType($this->_product->getTypeId());

        if (is_array($prices) && count($prices)) {
            $data['price'] = $prices[0];
        }

        $data['priceCurrency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        $availability = Mage::helper('mageworx_seomarkup')->getAvailability($this->_product);
        if ($availability) {
            $data['availability'] = $availability;
        }

        $condition = Mage::helper('mageworx_seomarkup')->getConditionValue($this->_product);
        if ($condition) {
            $data['itemCondition'] = $condition;
        }

        $paymentMethods = Mage::helper('mageworx_seomarkup')->getPaymentMethods();
        if ($paymentMethods) {
            $data['acceptedPaymentMethod'] = $paymentMethods;
        }

        $deliveryMethods = Mage::helper('mageworx_seomarkup')->getDeliveryMethods();
        if ($deliveryMethods) {
            $data['availableDeliveryMethod'] = $deliveryMethods;
        }

        return $data;
    }
}