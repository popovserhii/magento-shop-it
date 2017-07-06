<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Converter extends Varien_Object
{
    /**
     * @var MageWorx_SeoXTemplates_Helper_Converter
     */
    protected $_helperConverter;

    protected $_item = null;

    protected $_isDynamically;

    abstract protected function __convert($vars, $templateCode);

    abstract protected function _stopProccess($templateCode);

    /**
     * Retrieve converted string from template code
     * @param Mage_Catalog_Model_Abstract $item
     * @param string $templateCode
     * @return string
     */
    public function convert($item, $templateCode, $isDynamically = false)
    {
        $this->_isDynamically = $isDynamically;
        $this->_helperConverter = Mage::helper('mageworx_seoxtemplates/converter');

        $templateCode = $this->_randomizeUndependentStaticValues($templateCode);

        if ($this->_stopProccess($templateCode)) {
            return $templateCode;
        }

        $this->_setItem($item);
        $vars = $this->__parse($templateCode);
        $convertValue = $this->__convert($vars, $templateCode);

        return $convertValue;
    }

    /**
     *
     * @param Mage_Catalog_Model_Abstract $item
     */
    protected function _setItem($item)
    {
        $this->_item = $item;
    }

    /**
     * Retrive parsed vars from template code
     * @param string $templateCode
     * @return array
     */
    protected function __parse($templateCode)
    {
        $vars = array();
        preg_match_all('~(\[(.*?)\])~', $templateCode, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            preg_match('~^((?:(.*?)\{(.*?)\}(.*)|[^{}]*))$~', $match[2], $params);
            array_shift($params);

            if (count($params) == 1) {
                $vars[$match[1]]['prefix']     = $vars[$match[1]]['suffix']     = '';
                $vars[$match[1]]['attributes'] = explode('|', $params[0]);
            }
            else {
                $vars[$match[1]]['prefix']     = $params[1];
                $vars[$match[1]]['suffix']     = $params[3];
                $vars[$match[1]]['attributes'] = explode('|', $params[2]);
            }
        }

        return $vars;
    }

    /**
     * Convert static values, marked as "randomize"
     *
     * [The Best Product||Our bestseller]: [name][manufacturer||brand {manufacturer|brand}]
     * Our bestseller: [name][manufacturer||brand {manufacturer|brand}]
     *
     * @param string $templateCode
     * @return string
     */
    protected function _randomizeUndependentStaticValues($templateCode)
    {
        preg_match_all('~(\[([^\[\{\}]*?\|\|[^\[\{\}]*?)\])~', $templateCode, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if(!empty($match[2])) {
                $value = $this->_helperConverter->randomize($match[2]);
                $templateCode = str_replace($match[0], $value, $templateCode);
            }
        }

        return $templateCode;
    }

    protected function _getRequestParams()
    {
        $params = array();

        $controller = Mage::app()->getFrontController();
        if (is_object($controller) && is_callable(array($controller, 'getRequest'))) {
            $request = $controller->getRequest();
            if (is_object($request)) {
                $params = $request->getParams();
            }
        }

        return $params;
    }

    /**
     * @param $id
     * @param $attribute
     * @param null $storeId
     * @return mixed
     */
    protected function _getRawCategoryAttributeValue($id, $attribute, $storeId = null)
    {
        $storeId = is_null($storeId) ? Mage::app()->getStore()->getId() : null;
        return Mage::getResourceModel('catalog/category')->getAttributeRawValue($id, $attribute, $storeId);
    }

}
