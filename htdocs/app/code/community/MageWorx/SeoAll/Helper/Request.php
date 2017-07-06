<?php
/**
 * MageWorx
 * SeoAll Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoAll
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoAll_Helper_Request extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @return string|null
     */
    public function getCurrentFullActionName()
    {
        $controller = Mage::app()->getFrontController();
        if (is_object($controller) && is_callable(array($controller, 'getAction'))) {
            $action = $controller->getAction();
            if (is_object($action) && is_callable(array($action, 'getFullActionName'))) {
                $actionName = $action->getFullActionName();
                if ($actionName) {
                    return $actionName;
                }
            }
        }
        return null;
    }
}