<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        if($title = Mage::helper('xsitemap')->getTitle()){
            $this->getLayout()->getBlock('head')->setTitle($title);
        }

        if($description = Mage::helper('xsitemap')->getMetaDescription()){
            $this->getLayout()->getBlock('head')->setDescription($description);
        }

        if($keywords    = Mage::helper('xsitemap')->getMetaKeywords()){
            $this->getLayout()->getBlock('head')->setKeywords($keywords);
        }
        $this->renderLayout();
    }

}