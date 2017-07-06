<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Helper_Price extends Mage_Core_Helper_Abstract
{
    public function getPricesByProductType($productTypeId, $product = null)
    {
        if ($productTypeId == 'bundle') {
            $prices = $this->getBundlePrices($product);
        }
        elseif ($productTypeId == 'grouped') {
            $prices = $this->getGroupedPrices($product);
        }
        elseif ($productTypeId == 'giftcard') {
            $prices = $this->getGiftcardPrices($product);
        }
        else {
            $prices = $this->getDefaultPrices($product);
        }

        return (!empty($prices)) ? $prices : null;
    }

    /**
     * @param string $productTypeId
     * @return MageWorx_SeoMarkup_Model_Richsnippet_Catalog_Product_Price_Abstract
     */
    public function getPriceByProductType($productTypeId)
    {
    	$productTypes = array(
			'simple'       => 'mageworx_seomarkup/richsnippet_catalog_product_price_default',
			'virtual'      => 'mageworx_seomarkup/richsnippet_catalog_product_price_default',
			'downloadable' => 'mageworx_seomarkup/richsnippet_catalog_product_price_default',
			'configurable' => 'mageworx_seomarkup/richsnippet_catalog_product_price_default',
			'grouped'      => 'mageworx_seomarkup/richsnippet_catalog_product_price_grouped',
			'bundle'       => 'mageworx_seomarkup/richsnippet_catalog_product_price_bundle',
			'giftcard'	   => 'mageworx_seomarkup/richsnippet_catalog_product_price_giftcard',
    	);

    	if (!empty($productTypes[$productTypeId])) {
    		return Mage::getModel($productTypes[$productTypeId]);
        }
    }

    public function getDefaultPrices($_product = null)
    {
        if (!$_product) {
            $_product = Mage::registry('current_product');
        }

        $_weeeHelper = Mage::helper('weee');
        $_taxHelper  = Mage::helper('tax');
        /* @var $_coreHelper Mage_Core_Helper_Data */
        /* @var $_weeeHelper Mage_Weee_Helper_Data */
        /* @var $_taxHelper Mage_Tax_Helper_Data */

        $_storeId           = $_product->getStoreId();
        $_simplePricesTax   = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
        $_minimalPriceValue = $_product->getMinimalPrice();
        $_minimalPrice      = $_taxHelper->getPrice($_product, $_minimalPriceValue, $_simplePricesTax);
        $prices             = array();


        if (!$_product->isGrouped()):
            $_weeeTaxAmount = $_weeeHelper->getAmountForDisplay($_product);
            if ($_weeeHelper->typeOfDisplay($_product,
                    array(Mage_Weee_Model_Tax::DISPLAY_INCL_DESCR, Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL,
                    4))):
                $_weeeTaxAmount = $_weeeHelper->getAmount($_product);
            endif;
            $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
            if ($_weeeHelper->isTaxable() && !$_taxHelper->priceIncludesTax($_storeId)):
                $_attributes             = $_weeeHelper->getProductWeeeAttributesForRenderer($_product, null, null,
                    null, true);

                $_weeeTaxAmountInclTaxes = $this->_getAmountInclTaxes($_weeeHelper, $_attributes);
            endif;

            $_price             = $_taxHelper->getPrice($_product, $_product->getPrice());
            $_regularPrice      = $_taxHelper->getPrice($_product, $_product->getPrice(), $_simplePricesTax);
            $_finalPrice        = $_taxHelper->getPrice($_product, $_product->getFinalPrice());
            $_finalPriceInclTax = $_taxHelper->getPrice($_product, $_product->getFinalPrice(), true);
            if ($_finalPrice >= $_price):
                if ($_taxHelper->displayBothPrices()):
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)): // including
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_price;
                    else:
                        $prices[] = $_finalPriceInclTax;
                        if ($_finalPrice == $_price):
                            $prices[] = $_price;
                        else:
                            $prices[] = $_finalPrice;
                        endif;
                    endif;
                else:
                    if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)): // including
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                        $prices[] = $_price + $_weeeTaxAmount;
                    elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                        $prices[] = $_price;
                        $prices[] = $_price + $_weeeTaxAmount;
                    else:
                        if ($_finalPrice == $_price):
                            $prices[] = $_price;
                        else:
                            $prices[] = $_finalPrice;
                        endif;
                    endif;
                endif;
            else: /* if ($_finalPrice == $_price): */
                $_originalWeeeTaxAmount = $_weeeHelper->getOriginalAmount($_product);

                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 0)): // including
                    if ($_taxHelper->displayBothPrices()):
                        $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                        $prices[] = $_finalPrice + $_weeeTaxAmount;
                    else:
                        $prices[] = $_finalPrice + $_weeeTaxAmountInclTaxes;
                    endif;
                    $prices[] = $_regularPrice + $_originalWeeeTaxAmount;
                elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 1)): // incl. + weee
                    $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                    $prices[] = $_finalPrice + $_weeeTaxAmount;
                    $prices[] = $_regularPrice + $_originalWeeeTaxAmount;
                elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 4)): // incl. + weee
                    $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                    $prices[] = $_finalPrice + $_weeeTaxAmount;
                    $prices[] = $_regularPrice + $_originalWeeeTaxAmount;
                elseif ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, 2)): // excl. + weee + final
                    $prices[] = $_finalPriceInclTax + $_weeeTaxAmountInclTaxes;
                    $prices[] = $_finalPrice;
                    $prices[] = $_regularPrice;
                else: // excl.
                    if ($_taxHelper->displayBothPrices()):
                        $prices[] = $_finalPriceInclTax;
                    else:
                        $prices[] = $_finalPrice;
                    endif;
                    $prices[] = $_regularPrice;
                endif;

            endif; /* if ($_finalPrice == $_price): */

            if ($_minimalPriceValue && $_minimalPriceValue < $_product->getFinalPrice()):

                $_minimalPriceDisplayValue = $_minimalPrice;
                if ($_weeeTaxAmount && $_weeeHelper->typeOfDisplay($_product, array(0, 1, 4))):
                    $_minimalPriceDisplayValue = $_minimalPrice + $_weeeTaxAmount;
                endif;
                $prices[] = $_minimalPriceDisplayValue;
            endif; /* if ($block->getDisplayMinimalPrice() && $_minimalPrice && $_minimalPrice < $_finalPrice): */

        else: /* if (!$_product->isGrouped()): */

            $_exclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue);
            $_inclTax = $_taxHelper->getPrice($_product, $_minimalPriceValue, true);

            if ($_minimalPriceValue):
                if ($_taxHelper->displayBothPrices()):
                    $prices[] = $_inclTax;
                    $prices[] = $_exclTax;
                else:
                    $_showPrice = $_inclTax;
                    if (!$_taxHelper->displayPriceIncludingTax()) {
                        $_showPrice = $_exclTax;
                    }

                    $prices[] = $_showPrice;
                endif;
            endif; /* if ($block->getDisplayMinimalPrice() && $_minimalPrice): */
        endif; /* if (!$_product->isGrouped()): */

        return $prices;
    }

    /**
     * @return array
     */
    public function getBundlePrices($_product = null)
    {
        if (!$_product) {
            $_product = Mage::registry('current_product');
        }

        $prices      = array();
        $_priceModel = $_product->getPriceModel();

        if (is_callable(array($_priceModel, 'getPricesDependingOnTax'))) {
        	list($_minimalPriceTax, $_maximalPriceTax) = $_priceModel->getPricesDependingOnTax($_product, null, null, false);
        	list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_priceModel->getPricesDependingOnTax($_product, null, true, false);
        } else {
            list($_minimalPrice, $_maximalPrice) = $_product->getPriceModel()->getPrices($_product);
			$_maximalPriceTax = $_minimalPriceTax = Mage::helper('tax')->getPrice($_product, $_minimalPrice);
			$_maximalPriceInclTax = $_minimalPriceInclTax = Mage::helper('tax')->getPrice($_product, $_minimalPrice, true);
        }

        $_weeeTaxAmount = 0;

        if ($_product->getPriceType() == 1) {
            $_weeeTaxAmount          = Mage::helper('weee')->getAmount($_product);
            $_weeeTaxAmountInclTaxes = $_weeeTaxAmount;
            if (Mage::helper('weee')->isTaxable()) {
                $_attributes             = Mage::helper('weee')->getProductWeeeAttributesForRenderer($_product, null,
                    null, null, true);
                $_weeeTaxAmountInclTaxes = $this->_getAmountInclTaxes(Mage::helper('weee'), $_attributes);
            }
            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(0, 1, 4))) {
                $_minimalPriceTax += $_weeeTaxAmount;
                $_minimalPriceInclTax += $_weeeTaxAmountInclTaxes;
            }
            if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, 2)) {
                $_minimalPriceInclTax += $_weeeTaxAmountInclTaxes;
            }
        }

        if ($_product->getPriceView()):
            if ($this->_displayBothPrices($_product)):
                $prices[] = $_minimalPriceInclTax;
                $prices[] = $_minimalPriceTax;
            else:
                $prices[] = $_minimalPriceTax;
                if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount):
                    $prices[] = $_minimalPriceInclTax;
                endif;
            endif;
        else:
            if ($_minimalPriceTax <> $_maximalPriceTax):
                if ($this->_displayBothPrices($_product)):
                    $prices[] = $_minimalPriceInclTax;
                    $prices[] = $_minimalPriceTax;
                else:
                    $prices[] = $_minimalPriceTax;
                    if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount):
                        $prices[] = $_minimalPriceInclTax;
                    endif;
                endif;

                if ($_product->getPriceType() == 1) {
                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, array(0, 1, 4))) {
                        $_maximalPriceTax += $_weeeTaxAmount;
                        $_maximalPriceInclTax += $_weeeTaxAmountInclTaxes;
                    }
                    if ($_weeeTaxAmount && Mage::helper('weee')->typeOfDisplay($_product, 2)) {
                        $_maximalPriceInclTax += $_weeeTaxAmountInclTaxes;
                    }
                }

                if ($this->_displayBothPrices($_product)):
                    $prices[] = $_maximalPriceInclTax;
                    $prices[] = $_maximalPriceTax;
                else:
                    $prices[] = $_maximalPriceTax;
                    if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount):
                        $prices[] = $_maximalPriceInclTax;
                    endif;
                endif;
            else:
                if ($this->_displayBothPrices($_product)):
                    $prices[] = $_minimalPriceInclTax;
                    $prices[] = $_minimalPriceTax;
                else:
                    $prices[] = $_minimalPriceTax;
                    if (Mage::helper('weee')->typeOfDisplay($_product, 2) && $_weeeTaxAmount):
                        $prices[] = $_minimalPriceInclTax;
                    endif;
                endif;
            endif;
        endif;

        return $prices;
    }

    public function getGroupedPrices($product = null)
    {
        if (!$product) {
            $product = Mage::registry('current_product');
        }
        $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);

        if (count($associatedProducts)) {
            $allProductPrices = array();
            foreach ($associatedProducts as $product) {
                $productPrices                                = $this->getDefaultPrices($product);
                $allProductPrices[(string) $productPrices[0]] = $productPrices;
            }
            if (count($allProductPrices)) {
                ksort($allProductPrices);
                return array_shift($allProductPrices);
            }
        }
        return $this->getDefaultPrices();
    }

    public function getGiftcardPrices($product = null)
    {
    	if (!$product) {
            $product = Mage::registry('current_product');
        }
    	return array($product->getPriceModel()->getMinAmount($product));
    }

    protected function _displayBothPrices($_product)
    {
    	if (is_callable(array($_product->getPriceModel(), 'getIsPricesCalculatedByIndex'))) {
    		if ($_product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
            	&& $_product->getPriceModel()->getIsPricesCalculatedByIndex() !== false) {
            	return false;
        	}
        	return Mage::helper('tax')->displayBothPrices();
    	} else {
    		return false;
    	}
    }

    protected function _getAmountInclTaxes($weeeHelper, $attributes)
    {
        if (method_exists($weeeHelper, 'getAmountInclTaxes')) {
            return $weeeHelper->getAmountInclTaxes($attributes);
        }

        if (is_array($attributes)) {
            $amount = 0;
            foreach ($attributes as $attribute) {
                /* @var $attribute Varien_Object */
                $amount += $attribute->getAmount() + $attribute->getTaxAmount();
            }
        } else {
            throw new Mage_Exception('$attributes must be an array');
        }

        return (float)$amount;
    }
}