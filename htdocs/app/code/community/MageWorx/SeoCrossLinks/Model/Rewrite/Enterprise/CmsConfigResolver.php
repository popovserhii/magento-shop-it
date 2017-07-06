<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

if ((string)Mage::getConfig()->getModuleConfig('MageWorx_SeoBase')->active == 'true') {
    class MageWorx_SeoCrossLinks_Model_Rewrite_Enterprise_CmsConfigResolver extends MageWorx_SeoBase_Model_Rewrite_Enterprise_CmsConfig {}
} else {
    class MageWorx_SeoCrossLinks_Model_Rewrite_Enterprise_CmsConfigResolver extends Enterprise_Cms_Model_Config {}
}