<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Adapter extends Mage_Core_Model_Abstract
{
    protected $_defaultStore;
    protected $_storeId;
    protected $_attributeCodes = array();
    protected $_converter;
    protected $_converterModelUri;
    protected $_collection;
    protected $_testMode;

    abstract protected function _apply($template);

    /**
     * Apply (write to database or report) template for special store
     * @param object $collection
     * @param MageWorx_SeoXTemplates_Model_Template $template
     * @param int|null $customStoreId
     * @param string $testMode
     */
    public function apply($collection, $template, $customStoreId = null, $testMode = null)
    {
        if (!$this->_initAdapter($collection, $template, $customStoreId, $testMode)) {
            return false;
        }
        $this->_apply($template);
    }

    public function getAttributeCodes()
    {
        return $this->_attributeCodes;
    }

    /**
     * Initialize adapter for special store
     * @param object $collection
     * @param MageWorx_SeoXTemplates_Model_Template $template
     * @param int $customStoreId
     * @param string $testMode
     * @return boolean
     */
    protected function _initAdapter($collection, $template, $customStoreId, $testMode)
    {
        if(!$collection){
            return false;
        }
        $this->_collection = $collection;
        $this->_storeId = (!is_null($customStoreId) ? $customStoreId : $template->getStoreId());
        $this->_testMode = $testMode;

        $this->_initConverter($template, $customStoreId);
        return true;
    }

    /**
     * Initialize converter
     * @param MageWorx_SeoXTemplates_Model_Template $template
     */
    protected function _initConverter($template)
    {
        $this->_converter = Mage::getModel($this->_converterModelUri);
        $this->_converter->setTemplate($template->getCode());
    }
}