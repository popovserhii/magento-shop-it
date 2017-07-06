<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoBase_Model_Factory_Action_Abstract extends Mage_Core_Model_Abstract
{
    abstract public function getModel($fullActionName = null);
}