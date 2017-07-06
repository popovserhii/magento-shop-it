<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoBase_Model_Robots_Abstract extends Mage_Core_Model_Abstract
{
    protected $_helperData;

    public function getRobots() {

        $this->_helperData     = Mage::helper('mageworx_seobase');
        $this->_fullActionName = $this->_helperData->getCurrentFullActionName();

        return $this->_getRobots();
    }

    abstract protected function _getRobots();

    /**
     * Retrive robots by config settings
     *
     * @return string
     */
    protected function _getRobotsBySettings()
    {
        $metaRobots = '';
        $this->_modifyByProtocol($metaRobots);
        $this->_modifyByPages($metaRobots, $this->_helperData->getNoindexPages(), 'NOINDEX, FOLLOW');
        $this->_modifyByUserPages($metaRobots, $this->_helperData->getNoindexUserPages(), 'NOINDEX, FOLLOW');
        $this->_modifyByUserPages($metaRobots, $this->_helperData->getNoindexNofollowUserPages(), 'NOINDEX, NOFOLLOW');

        return $metaRobots;
    }

    /**
     * Retrive robots for URL protocol
     *
     * @param string $metaRobots
     * @return string $metaRobots
     */
    protected function _modifyByProtocol(&$metaRobots)
    {
        if (substr(Mage::helper('core/url')->getCurrentUrl(), 0, 8) == 'https://') {
            $metaRobots = $this->_helperData->getMetaRobotsForHttps();
        }
    }

    /**
     * Retrive robots for page settings
     *
     * @param string $metaRobots
     * @return string $metaRobots
     */
    protected function _modifyByPages(&$metaRobots, $patterns, $robots)
    {
        if (empty($patterns)) {
            return;
        }        
        foreach ($patterns as $pattern) {
            if (preg_match('/' . $pattern . '/', $this->_helperData->getCurrentFullActionName())) {
                $metaRobots = $robots;
                break;
            }
        }
    }

    /**
     * Retrive robots for user page settings
     *
     * @param string $metaRobots
     * @return string $metaRobots
     */
    protected function _modifyByUserPages(&$metaRobots, $patterns, $robots)
    {
        if (empty($patterns)) {
            return;
        }
        foreach ($patterns as $pattern) {
            $pattern = str_replace(array('?', '*'), array('\?', '.*?'), $pattern);

            if (preg_match('#' . $pattern . '#', $this->_helperData->getCurrentFullActionName())
                || preg_match('#' . $pattern . '#', Mage::helper('core/url')->getCurrentUrl())
            ) {
                $metaRobots = $robots;
                break;
            }
        }
    }
}