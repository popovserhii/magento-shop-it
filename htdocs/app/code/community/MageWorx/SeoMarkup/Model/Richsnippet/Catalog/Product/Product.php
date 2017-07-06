<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

/**
 * @see MageWorx_SeoMarkup_Block_Catalog_Product_View
 */
class MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Product extends MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Abstract
{
    protected $_availabilityByContentUri = 'mageworx_seomarkup/richsnippet_catalog_product_availability';
    protected $_metaCategoryUri          = 'mageworx_seomarkup/richsnippet_catalog_product_meta_category';
    protected $_imageUri                 = 'mageworx_seomarkup/richsnippet_catalog_product_image';
    protected $_descriptionUri           = 'mageworx_seomarkup/richsnippet_catalog_product_description';
    protected $_metaDescriptionUri       = 'mageworx_seomarkup/richsnippet_catalog_product_meta_description';
    protected $_metaSkuUri               = 'mageworx_seomarkup/richsnippet_catalog_product_meta_sku';
    protected $_metaPaymentUri           = 'mageworx_seomarkup/richsnippet_catalog_product_meta_payment';
    protected $_metaDeliveryUri          = 'mageworx_seomarkup/richsnippet_catalog_product_meta_delivery';
    protected $_metaColorUri             = 'mageworx_seomarkup/richsnippet_catalog_product_meta_color';
    protected $_metaManufacturerUri      = 'mageworx_seomarkup/richsnippet_catalog_product_meta_manufacturer';
    protected $_metaBrandUri             = 'mageworx_seomarkup/richsnippet_catalog_product_meta_brand';
    protected $_metaModelUri             = 'mageworx_seomarkup/richsnippet_catalog_product_meta_model';
    protected $_metaGtinUri              = 'mageworx_seomarkup/richsnippet_catalog_product_meta_gtin';
    protected $_metaHeightUri            = 'mageworx_seomarkup/richsnippet_catalog_product_meta_height';
    protected $_metaWidthUri             = 'mageworx_seomarkup/richsnippet_catalog_product_meta_width';
    protected $_metaDepthUri             = 'mageworx_seomarkup/richsnippet_catalog_product_meta_depth';
    protected $_metaWeightUri            = 'mageworx_seomarkup/richsnippet_catalog_product_meta_weight';
    protected $_metaConditionUri         = 'mageworx_seomarkup/richsnippet_catalog_product_meta_condition';
    protected $_metaSellerUri            = 'mageworx_seomarkup/richsnippet_catalog_product_meta_seller';   
    protected $_metaCustomPropertyUri    = 'mageworx_seomarkup/richsnippet_catalog_product_meta_custom';
    protected $_metaSocialUri            = 'mageworx_seomarkup/richsnippet_catalog_product_meta_social';

    protected function _addAttributeForNodes(simple_html_dom_node $node)
    {
        $commonNode = $this->_findCommonContainer($node);
        $parentNode = $this->_findParentContainer($node);

        if ($commonNode && $parentNode) {
            $commonNode->itemtype  = "http://schema.org/Product";
            $commonNode->itemscope = "";
            $parentNode->itemprop  = "name";
            return true;
        }
        return false;
    }

    protected function _afterRender()
    {       
        $helperConfig = Mage::helper('mageworx_seomarkup/config');

        //Availability must be first!
        $availability = Mage::getModel($this->_availabilityByContentUri);
        if (!$availability->render($this->_html, $this->_block)) {
            //$this->_errorRenderer($helperLogger->__("Product availability property wasn't added."));
        }

        //Category must be second!
        $category = Mage::getModel($this->_metaCategoryUri);
        $category->render($this->_html, $this->_block);

        if ($helperConfig->isRichsnippetPaymentEnabled()) {
            $metaPayment = Mage::getModel($this->_metaPaymentUri);
            if (!$metaPayment->render($this->_html, $this->_block)) {
                //$this->_errorRenderer($helperLogger->__("Product payment methods wasn't added."));
            }
        }

        if ($helperConfig->isRichsnippetDeliveryEnabled()) {
            $metaDelivery = Mage::getModel($this->_metaDeliveryUri);
            if (!$metaDelivery->render($this->_html, $this->_block)) {
                //$this->_errorRenderer($helperLogger->__("Product delivery methods wasn't added."));
            }
        }

        $image = Mage::getModel($this->_imageUri);
        $image->render($this->_html, $this->_block);

        $description = Mage::getModel($this->_descriptionUri);
        if (!$description->render($this->_html, $this->_block)) {
            $metaDescription = Mage::getModel($this->_metaDescriptionUri);
            $metaDescription->render($this->_html, $this->_block);
        }

        if ($helperConfig->isRichsnippetSellerEnabled()) {
            $seller = Mage::getModel($this->_metaSellerUri);
            $seller->render($this->_html, $this->_block);
        }

        Mage::getModel($this->_metaSkuUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaColorUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaManufacturerUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaBrandUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaModelUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaGtinUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaHeightUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaWidthUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaDepthUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaWeightUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaConditionUri)->render($this->_html, $this->_block);
        Mage::getModel($this->_metaCustomPropertyUri)->render($this->_html, $this->_block);

        return true;
    }

    protected function _beforeInit($html)
    {
        $html = parent::_beforeInit($html);
        if(!$html){
            return false;
        }

        if (!is_object($html)) {
            $html = $this->_magentoHtmlFix($html);
        }
        return $html;
    }

    protected function _beforeRender($html)
    {
        $priceReport = $this->_getPriceReportObject();
        if (is_object($priceReport) && $priceReport->getStatus() == 'success') {
            return parent::_beforeRender($html);
        }
        else {
            $res = $this->_renderPrice();
            if ($res != false) {
                return parent::_beforeRender($html);
            }
        }
        return false;
    }

    protected function _renderPrice()
    {
        $price = Mage::helper('mageworx_seomarkup/price')->getPriceByProductType($this->_product->getTypeID());
        if (!$price) {        	
        	$this->_errorRenderer(Mage::helper('mageworx_seomarkup')->__("Unknow product type. Richsnippets were disabled."));
        	return false;
        }
        
        $res = $price->render($this->_html, $this->_block, false);
        if ($res != false) {
            return true;
        }
        return false;
    }

    /**
     * Find highest common container for nested items: offer and rating
     * @param simple_html_dom_node $node
     * @return simple_html_dom_node | false
     */
    protected function _findCommonContainer(simple_html_dom_node $node)
    {
        $priceFlag  = false;
        //If the rating wasn't added that generation proceeds...
        $ratingFlag = true;
        if (is_object($this->_getAggregateRatingReportObject())) {
            if ($this->_getAggregateRatingReportObject()->getStatus() == "success") {
                $ratingFlag = false;
            }
        }

        $node = clone $node;
        while ($node = $node->parent) {
            if ($node->tag == "root") {
                return false;
            }
            if (in_array($node->tag, $this->_container)) {
                if (!$ratingFlag) {
                    $resultRating = $node->find('*[itemprop=aggregateRating]');
                    if (is_array($resultRating) && count($resultRating)) {
                        $ratingFlag = true;
                    }
                }
                if (!$priceFlag) {
                    $resultPrice = $node->find('*[itemprop="offers"]');
                    if (is_array($resultPrice) && count($resultPrice)) {
                        $priceFlag = true;
                    }
                }
                if ($ratingFlag && $priceFlag && $node->parent->tag == 'root') {
                    return $node;
                }
            }
        }
        return false;
    }

    protected function _isValidNode(simple_html_dom_node $node)
    {
        //will be product name property
        $parentNode = $this->_findParentContainer($node);
        if (!$parentNode) {
            return false;
        }
        //will be main product item
        if (!$this->_isNotInsideTypes($node)) {
            return false;
        }

        if (!$this->_findCommonContainer($parentNode)) {
            return false;
        }
        return $node;
    }

    /**
     * @return Varien_Object or false
     */
    protected function _getAggregateRatingReportObject()
    {
//        return new Varien_Object(array('status'=>'success', 'tag'=>'div'));
        return Mage::registry('mageworx_richsnippet_aggregate_rating_report');
    }

    /**
     * @return Varien_Object or false
     */
    protected function _getPriceReportObject()
    {
        return Mage::registry('mageworx_richsnippet_price_report');
    }

    protected function _checkBlockType()
    {
        return true;
    }

    protected function _getItemValues()
    {
        return array(Mage::helper('catalog/output')->productAttribute($this->_product, $this->_product->getName(), $this->_product->getName(), 'name'));
    }

    protected function _magentoHtmlFix($html)
    {
        /*
         * Magento code without space between |name="bundle_option[*]"| and |value="*"|
         * Html parser crop |value="*"|
         */

        /*
         * Example code:
          <div class="input-box">
          <ul class="options-list">
          <li><input type="radio" onclick="bundle.changeSelection(this)"
          class="radio validate-one-required-by-name change-container-classname"
          id="bundle-option-20-54" name="bundle_option[20]"value="54"/>
         */

        if ($this->_product->getTypeId() == 'bundle') {
            $html = str_replace("\"value=\"", "\" value=\"", $html);
        }
        return $html;
    }

}