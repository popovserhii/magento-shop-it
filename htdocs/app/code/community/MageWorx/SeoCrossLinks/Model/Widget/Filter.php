<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Widget_Filter extends Mage_Cms_Model_Template_Filter
{
    /**
     * Replace widget codes to hash.
     *
     * @param string $value
     * @param array $replacedPairs
     * @return string
     */
    public function replace($value, &$replacedPairs)
    {
        $helperFunction = Mage::helper('mageworx_seocrosslinks/function');

        // "depend" and "if" operands should be first
        foreach (array(
            self::CONSTRUCTION_DEPEND_PATTERN,
            self::CONSTRUCTION_IF_PATTERN,
            ) as $pattern) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    $replacedValue = $this->_getRandomValue();
                    $replacedPairs[$replacedValue] = $construction[0];
                    $value = $helperFunction->strReplaceOnce($construction[0], $replacedValue, $value);
                }
            }
        }

        if (preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
				$replacedValue = $this->_getRandomValue();
                $replacedPairs[$replacedValue] = $construction[0];                
                $value = $helperFunction->strReplaceOnce($construction[0], $replacedValue, $value);
            }
        }
        return $value;
    }

    /**
     *
     * @return string
     */
    protected function _getRandomValue()
    {
        return substr(md5(rand()), 0, 9);
    }
}
