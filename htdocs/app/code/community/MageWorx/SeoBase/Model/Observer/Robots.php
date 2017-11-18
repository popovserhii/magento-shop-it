<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_Robots extends Mage_Core_Model_Abstract
{
    protected $_out = false;

    public function setRobots($observer)
    {
        $action = $observer->getAction();
        $layout = $observer->getLayout();

        if (!$layout || !$action) {
            return;
        }

        $request = Mage::app()->getRequest();
        if ($request && $request->isXmlHttpRequest()) {
            return;
        }

        $headBlock     = $layout->getBlock('head');

        if ($headBlock) {
            $robotsFactory = Mage::getModel('mageworx_seobase/factory_action_robotsFactory');
            $fullActionName = $action->getFullActionName() ? $action->getFullActionName() : null;
            $robotsModel = $robotsFactory->getModel($fullActionName);

            if(!$robotsModel) {
                return;
            }

            $robots = $robotsModel->getRobots();

            if ($robots) {
                $headBlock->setRobots($robots);
            }
        }
    }
}