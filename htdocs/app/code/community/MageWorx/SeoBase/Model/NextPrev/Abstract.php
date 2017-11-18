<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoBase_Model_NextPrev_Abstract extends Mage_Core_Model_Abstract
{
    const ENABLE_NEXT_PREV     = 1;
    const DISABLE_NEXT_PREV    = 0;

    protected $_helperData;

    /**
     * @var string
     */
    protected $_prevUrl = null;

    /**
     * @var string
     */
    protected $_nextUrl = null;

    /**
     * @var bool
     */
    protected $_initFlag;

    abstract public function getNextUrl();

    abstract public function getPrevUrl();

    abstract protected function _getPager();

    /**
     * Initialize
     *
     * @return this
     */
    protected function _initNextPrev()
    {
        if ($this->_initFlag) {
            return $this;
        }

        $this->_helperData = Mage::helper('mageworx_seobase');

        $pager = $this->_getPager();
        if (!is_object($pager)) {
            $this->_initFlag = true;
            return $this;
        }

        if (!$pager->getCollection()) {
            $this->_initFlag = true;
            return $this;
        }

        if ($pager->getLastPageNum() > 1) {

            if (!$pager->isLastPage()) {
                $this->_nextUrl  = $pager->getNextPageUrl();
            }

            $pageVarName = $pager->getPageVarName();
            if ($pager->getCurrentPage() == 2) {
                $this->_prevUrl = $this->_removeFirstPage($pager->getPreviousPageUrl(), $pageVarName);
            }
            elseif ($pager->getCurrentPage() > 2) {
                $this->_prevUrl = $pager->getPreviousPageUrl();
            }
        }
        $this->_initFlag = true;
        return $this;
    }

    /**
     * Remove first page params
     *
     * @param string $url
     * @param string $pName
     * @return string
     */
    protected function _removeFirstPage($url, $pName = 'p')
    {
        return str_replace(
            array(
                '?' . $pName . '=1&amp;',
                '?' . $pName . '=1&',
                '&amp;' . $pName . '=1&amp;',
                '&' . $pName . '=1&',
                '?' . $pName . '=1',
                '&' . $pName . '=1',
                '&amp;' . $pName . '=1'
            ),
            array('?', '?', '&amp;', '&'),
            $url
        );
    }
}