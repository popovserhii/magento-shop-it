<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Converter extends Mage_Core_Helper_Abstract
{
    const STATIC_VALUE_DELIMITER = '||';

    /**
     * @param $rawValue
     * @return string
     */
    public function randomize($rawValue)
    {
        if (strpos($rawValue, self::STATIC_VALUE_DELIMITER) === false) {
            return $rawValue;
        }

        $lValue = ltrim($rawValue);
        $leftSpaceCount = strlen($rawValue) - strlen($lValue);

        $rValue = rtrim($rawValue);
        $rightSpaceCount = strlen($rawValue) - strlen($rValue);

        $trimValue = trim($rawValue);

        $values = explode(self::STATIC_VALUE_DELIMITER, $trimValue);
        $value  = str_repeat(' ', $leftSpaceCount) . $values[array_rand($values)] . str_repeat(' ', $rightSpaceCount);

        return $value;
    }

    /**
     * @param string $rawValue
     * @return string
     */
    public function randomizePrefix($rawValue)
    {
        return $this->randomize($rawValue);
    }

    /**
     * @param string $rawValue
     * @return string
     */
    public function randomizeSuffix($rawValue)
    {
        return $this->randomize($rawValue);
    }
}
