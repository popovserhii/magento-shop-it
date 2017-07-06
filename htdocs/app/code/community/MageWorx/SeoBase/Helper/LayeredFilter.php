<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Helper_LayeredFilter extends Mage_Core_Helper_Abstract
{

    /**
     * Admin config setting
     */
    const CATEGORY_LN_CANONICAL_OFF          = 0;
    const CATEGORY_LN_CANONICAL_USE_FILTERS  = 1;
    const CATEGORY_LN_CANONICAL_CATEGORY_URL = 2;

    /**
     * Attribut individual setting
     */
    const ATTRIBUTE_LN_CANONICAL_BY_CONFIG    = 0;
    const ATTRIBUTE_LN_CANONICAL_USE_FILTERS  = 1;
    const ATTRIBUTE_LN_CANONICAL_CATEGORY_URL = 2;

    public function isLNFriendlyUrlsEnabled()
    {
        return false;
    }

    /**
     * Determines by global value from a config and to value (based on attributes setting and position)
     * existence of filters in canonical url.
     *
     * @return boolean
     */
    public function isIncludeLNFiltersToCanonicalUrl()
    {
        $enableByConfig  = Mage::helper('mageworx_seobase')->isIncludeLNFiltersToCanonicalUrlByConfig();
        $answerByFilters = $this->isIncludeLNFiltersToCanonicalUrlByFilters();

        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_CATEGORY_URL) {
            return false;
        }

        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_CATEGORY_URL && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_USE_FILTERS) {
            return true;
        }
        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS) {
            return true;
        }
        return false;
    }

    public function isIncludeLNFiltersToCanonicalUrlByFilters()
    {
        $filtersData = Mage::helper('mageworx_seoall/layeredFilter')->getLayeredNavigationFiltersData();

        if (!$filtersData) {
            return 'default';
        }
        usort($filtersData, array($this, "_cmp"));
        foreach ($filtersData as $data) {
            if (!empty($data['use_in_canonical'])) {
                return $data['use_in_canonical'];
            }
        }
        return false;
    }

    /**
     * @param int $a
     * @param int $b
     * @return int
     */
    protected function _cmp($a, $b)
    {
        $a['position'] = (empty($a['position'])) ? 0 : $a['position'];
        $b['position'] = (empty($b['position'])) ? 0 : $b['position'];

        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? +1 : -1;
    }
}