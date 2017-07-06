<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Url extends Mage_Core_Helper_Abstract
{
    /**
     * Parse url and retrieve page number.
     * At first find by param '[?&]p=', then by part of url (E.g. ex.com/apparel-page2.html).
     * @return bool|string
     */
    public function getPageNumFromUrl()
    {
        $params = Mage::app()->getFrontController()->getRequest()->getParams();
        if (!empty($params['p'])) {
            if (settype($params['p'], 'int') == $params['p']) {
                $num = $params['p'];
            }
        }

        if (empty($num) && $this->isModuleEnabled('MageWorx_SeoFriendlyLN')) {
            $pageFormat = Mage::helper('seofriendlyln/config')->getPagerUrlFormat();
            if ($pageFormat && $pageFormat != '-[page_number]') {
                $pattern = '(' . str_replace('[page_number]', '[0-9]+', $pageFormat) . ')';
                if (preg_match($pattern,
                    Mage::app()->getFrontController()->getAction()->getRequest()->getRequestString(), $matches)) {
                    $match = array_pop($matches);
                    if ($match) {
                        $lengthBeforeNum = strpos($pageFormat, '[page_number]');
                        $lengthAfterNum  = strlen($pageFormat) - (strpos($pageFormat, '[page_number]') + 13);
                        $num             = substr($match, $lengthBeforeNum);
                        $num             = substr($num, 0, strlen($num) - $lengthAfterNum);
                    }
                }
            }
        }
        return !empty($num) ? $num : false;
    }
}