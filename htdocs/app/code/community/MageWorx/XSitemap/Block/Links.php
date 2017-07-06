<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Block_Links extends Mage_Core_Block_Template
{
    const XML_PATH_ADD_LINKS         = 'mageworx_seo/xsitemap/add_links';
    const XML_PATH_SHOW_FOOTER_LINKS = 'mageworx_seo/xsitemap/show_footer_links';

    protected $_links;

    protected function _prepareLayout()
    {
        $links = array();
        if (Mage::getStoreConfigFlag(self::XML_PATH_SHOW_FOOTER_LINKS)) {
            $block = $this->getLayout()->getBlock('footer_links');
            if ($block) {
                $footerLinks = $block->getLinks();
                if (count($footerLinks)) {
                    foreach ($footerLinks as $link) {
                        $links[] = $link;
                    }
                }
            }
        }

        $addLinks = array_filter(preg_split('/\r?\n/', Mage::getStoreConfig(self::XML_PATH_ADD_LINKS)));

        if (count($addLinks)) {
            foreach ($addLinks as $link) {
                $_link = explode(',', $link, 2);
                if (count($_link) == 2) {

                    $helperTrailingSlash = Mage::helper('mageworx_seoall/trailingSlash');

                    if (strpos($_link[0], 'http') !== false){
                        $links[] = new Varien_Object(array('label' => trim($_link[1]),
                            'url' =>  trim($_link[0])));
                    } else {
                        $links[] = new Varien_Object(array('label' => trim($_link[1]),
                            'url' => $helperTrailingSlash->trailingSlash('link', Mage::getUrl((string) trim($_link[0])))));
                    }
                }
            }
        }

        $this->setLinks($links);
        return $this;
    }
}
