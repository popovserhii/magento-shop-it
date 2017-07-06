<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Helper_Function extends Mage_Core_Helper_Abstract
{
    /**
     * Recursive applies the callback to the elements of the given array
     *
     * @param string $func
     * @param array $array
     * @return array
     */
    public function arrayMapRecursive($func, $array)
    {
        if(!is_array($array)){
            $array = array();
        }

        foreach ($array as $key => $val) {
            if (is_array( $array[$key])) {
                $array[$key] = $this->arrayMapRecursive($func, $array[$key]);
            } else {
                $array[$key] = call_user_func($func, $val);
            }
        }
        return $array;
    }

    /**
     * Replace once occurrence of the search string with the replacement string
     *
     * @param string $search
     * @param string $replace
     * @param string $text
     * @return string
     */
    public function strReplaceOnce($search, $replace, $text)
    {
       $pos = mb_strpos($text, $search);
       return $pos !== false ? $this->mbSubstrReplace($text, $replace, $pos, mb_strlen($search)) : $text;
    }

    /**
     *
     * @param array|string $string
     * @param array|string $replacement
     * @param array|string $start
     * @param array|string $length
     * @return string
     */
    public function mbSubstrReplace($string, $replacement, $start, $length = null)
    {
        if (is_array($string)) {
            $num = count($string);

            if (is_array($replacement)) {
                $replacement = array_slice($replacement, 0, $num);
            } else {
                $replacement = array_pad(array($replacement), $num, $replacement);
            }

            if (is_array($start)) {
                $start = array_slice($start, 0, $num);
                foreach ($start as $key => $value) {
                    $start[$key] = is_int($value) ? $value : 0;
                }
            }
            else {
                $start = array_pad(array($start), $num, $start);
            }

            if (!isset($length)) {
                $length = array_fill(0, $num, 0);
            }
            elseif (is_array($length)) {
                $length = array_slice($length, 0, $num);
                foreach ($length as $key => $value) {
                    $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
                }
            }
            else {
                $length = array_pad(array($length), $num, $length);
            }
            return array_map(__FUNCTION__, $string, $replacement, $start, $length);
        }

        if( mb_internal_encoding() == "UTF-8" ) {
            preg_match_all('/./us', (string)$string, $smatches);
            preg_match_all('/./us', (string)$replacement, $rmatches);
        } else {
            preg_match_all('/./s', (string)$string, $smatches);
            preg_match_all('/./s', (string)$replacement, $rmatches);
        }

        if ($length === null) {
            $length = mb_strlen($string);
        }
        array_splice($smatches[0], $start, $length, $rmatches[0]);
        return join($smatches[0]);
    }
}