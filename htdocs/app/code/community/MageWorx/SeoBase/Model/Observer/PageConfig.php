<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_PageConfig extends Mage_Core_Model_Abstract
{

    public function addAttributeToRevision($observer)
    {
        $pageConfig = $observer->getObject();
        $pageConfig->addRevisionControlledAttribute('page', 'meta_robots');
        $pageConfig->addRevisionControlledAttribute('page', 'meta_title');
        $pageConfig->addRevisionControlledAttribute('page', 'mageworx_hreflang_identifier');
    }

}