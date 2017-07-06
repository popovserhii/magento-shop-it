<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoAll_Model_UrlRenderer_TrailingSlash_Abstract
{
    const ADD_TRAILING_SLASH_AFTER_GET_PARAMS = false;

    /**
     * Value for crop slash
     */
    const TRAILING_SLASH_CROP     = 'crop';

    /**
     * Value for add slash
     */
    const TRAILING_SLASH_ADD      = 'add';

    /**
     * Value for default action
     */
    const TRAILING_SLASH_DEFAULT  = 'default';


    /**
     *
     * @var MageWorx_SeoAll_Helper_Data
     */
    protected $_helperData;

    /**
     * @var MageWorx_SeoAll_Helper_Adapter
     */
    protected $_helperAdapter;


    /**
     * @var array
     */
    protected $_excludeExtensions = array('rss', 'html', 'htm', 'xml', 'php');

    /**
     *
     * @param string $url
     * @param int|null $storeId
     * @return string
     */
    public function trailingSlash($url, $storeId = null)
    {
        $this->_init();

        $trailingSlash = $this->_helperData->getTrailingSlashAction($storeId);

        if ($trailingSlash == self::TRAILING_SLASH_DEFAULT) {
            $trailingSlash = $this->_getDefaultTrailingSlashMethod();
        }

        if ($trailingSlash == self::TRAILING_SLASH_DEFAULT) {

        }
        elseif ($trailingSlash == self::TRAILING_SLASH_ADD) {
            $url = $this->_addTrailingSlash($url);
        }
        elseif ($trailingSlash == self::TRAILING_SLASH_CROP) {
            $url = $this->_cropTrailingSlash($url);
        }

        return $url;
    }

    protected function _init()
    {
        $this->_helperData    = Mage::helper('mageworx_seoall');
        $this->_helperAdapter = Mage::helper('mageworx_seoall/adapter');
    }

    /**
     * Retrieve action code: add|crop|default
     *
     * @return string
     */
    protected function _getDefaultTrailingSlashMethod()
    {
        return self::TRAILING_SLASH_DEFAULT;
    }

    /**
     *
     * @param string $rawUrl
     * @return string
     */
    protected function _addTrailingSlash($rawUrl)
    {
        $url = rtrim($rawUrl);

        if(strpos($url, '?') === false && !self::ADD_TRAILING_SLASH_AFTER_GET_PARAMS){
            if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $this->_excludeExtensions)) {
                $url.= '/';
            }
        }

        return $url;
    }

    /**
     *
     * @param string $url
     * @return string
     */
    protected function _cropTrailingSlash($url)
    {
        return rtrim(rtrim($url), '/');
    }
}