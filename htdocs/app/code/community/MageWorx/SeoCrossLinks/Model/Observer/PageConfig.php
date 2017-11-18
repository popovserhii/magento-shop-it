<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Observer_PageConfig extends Mage_Core_Model_Abstract
{

    public function addAttributeToRevision($observer)
    {
        $pageConfig = $observer->getObject();
        $pageConfig->addRevisionControlledAttribute('page', 'exclude_from_crosslinking');
    }

}