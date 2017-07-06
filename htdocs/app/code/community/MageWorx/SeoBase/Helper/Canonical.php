<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Helper_Canonical extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getPersonalCanonicalUrlCode($product)
    {
        $string = trim($product->getCanonicalUrl());

        return (preg_match('/^[0-9]+\_{1}[0-9]+$/', $string) === 1) ? $string : '';
    }

    /**
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getPersonalCanonicalUrlPath($product)
    {
        $string = trim($product->getCanonicalUrl());

        return (preg_match('/^[0-9]+\_{1}[0-9]+$/', $string) !== 1) ? $string : '';
    }
}