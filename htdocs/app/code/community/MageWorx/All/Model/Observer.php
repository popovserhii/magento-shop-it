<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Model_Observer
{
    /**
     * Remove not permitted groups from System Configuration Section
     *
     * @param  Varien_Event_Observer $observer
     * @return MageWorx_All_Model_Observer
     */
    public function restrictGroupsAcl($observer)
    {
        $editBlock = $observer->getEvent()->getBlock();

        if (!($editBlock instanceof Mage_Adminhtml_Block_System_Config_Edit)) {
            return $this;
        }

        $sectionCode = Mage::app()->getRequest()->getParam('section');
        if (false === strpos($sectionCode, 'mageworx')) {
            return $this;
        }

        $session = Mage::getSingleton('admin/session');
        $currentSection = Mage::getSingleton('adminhtml/config')->getSections()->$sectionCode;
        $groups = $currentSection->groups[0];
        foreach ($groups as $group => $object) {
            if (!$session->isAllowed("system/config/$sectionCode/$group")) {
                $currentSection->groups->$group = null;
            }
        }
        return $this;
    }

    /**
     *
     * @param  Varien_Event_Observer $observer
     * @return MageWorx_All_Model_Observer
     */
    public function showDeprecatedCodeNotice($observer)
    {
        if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
            return;
        }

        if ($this->_isAjax()) {
            return;
        }

        $moduleListHelper    = Mage::helper('mageworx_all/moduleList');
        $compatibilityHelper = Mage::helper('mageworx_all/compatibility');

        foreach (array_keys($moduleListHelper->getModuleList()) as $moduleName) {
            list($vendorName, $moduleName) = explode('_', $moduleName, 2);
            if (!$vendorName || !$moduleName) {
                continue;
            }

            if (!$compatibilityHelper->isDuplicateCodePool($moduleName, $vendorName)) {
                continue;
            }

            $title        = $vendorName . ' ' . $moduleName . ' - duplicate code detected.';
            $notification = Mage::getModel('adminnotification/inbox');
            if (is_object($notification)) {

                $issetMessage = $notification->getCollection()->addFieldToFilter('title', array('in' => array($title)))->count();
                if ($issetMessage) {
                    continue;
                }
                $url = "http://support.mageworx.com/extensions/general_questions/extensions_moved_to_community.html";

                $description = $compatibilityHelper->__(
                    'We have detected an old version of the extension "%s" installed in "%s".',
                    $vendorName . ' ' . $moduleName,
                    $compatibilityHelper->getExtensionDirInLocalScope($moduleName, $vendorName)
                );

                $description .= ' ' . $compatibilityHelper->__(
                    'This directory should be deleted to ensure that the the latest extension version works correctly.'
                );

                if (method_exists($notification, 'addMajor')) {
                    $notification->addMajor($title, $description, $url);
                }
            }
        }

        return $observer;
    }

    /**
     *
     * @return boolean
     */
    protected function _isAjax()
    {
        $request = Mage::app()->getRequest();
        if (!is_object($request)) {
            return false;
        }
        if ($request->isXmlHttpRequest()) {
            return true;
        }
        if ($request->getParam('ajax') || $request->getParam('isAjax')) {
            return true;
        }
        return false;
    }
}