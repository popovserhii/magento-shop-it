<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_XSitemap_Adminhtml_Mageworx_XsitemapController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        $this->showErrorIfExtensionInLocalCodePool();
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/xsitemap_generate')
            ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('xsitemap')->__('Google Sitemap (Extended)'),
                Mage::helper('xsitemap')->__('Google Sitemap (Extended)'))
        ;
        return $this;
    }

    protected function showErrorIfExtensionInLocalCodePool()
    {
        $helper = Mage::helper('xsitemap/compatibility');
        if (!$helper->oldConfigXmlExists()) {
            return;
        }

        $this->_getSession()->addError(
            $this->__(
                'We\'ve detected an old version of the extension "MageWorx_XSitemap", installed on the path to "%s". '
                . 'This directory should be deleted to ensure the correct work of the latest version.',
                $helper->getExtensionDirInLocalScope()
            )
        );
    }

    public function indexAction()
    {
        $this->_addReindexProblemNotification();
        $this->_addBrowserCacheNotification();
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('xsitemap/adminhtml_xsitemap'))
            ->renderLayout();
    }

    protected function _addReindexProblemNotification()
    {
        $msg  = $this->__('If you have changed any settings that modify product or category URLs before sitemap generation, please reindex \'Catalog URL Rewrites\' in \'Index Management\'.<br>');
        $msg .= $this->__('If you have found such URLs as \'.../catalog/product/view/...\' in sitemap, please also reindex \'Catalog URL Rewrites\'.<br>');
        return Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    protected function _addBrowserCacheNotification()
    {
        $msg = $this->__('It is possible that your browser could have cached XML sitemap, in order to avoid this, add new random param for sitemap URL every time you review XML sitemap, for example: http://store.com/sitemap_file.xml<font color = #eb5e00>?any_param</font>');
        return Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    protected function _addSitemapsConflictNotification()
    {
        $msg  = $this->__('Please, do not create folder or file named "sitemap" in root host category as it may lead to a conflict with HTML sitemap, which is opened via  http://store.com/sitemap/ .');
        return Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id    = $this->getRequest()->getParam('sitemap_id');
        $model = Mage::getModel('xsitemap/sitemap');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xsitemap')->__('This sitemap no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('sitemap_sitemap', $model);

        $this->_addSitemapsConflictNotification();

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('xsitemap')->__('Edit Sitemap') : Mage::helper('xsitemap')->__('New Sitemap'),
                $id ? Mage::helper('xsitemap')->__('Edit Sitemap') : Mage::helper('xsitemap')->__('New Sitemap'))
            ->_addContent($this->getLayout()->createBlock('xsitemap/adminhtml_xsitemap_edit'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('xsitemap/sitemap');

            if ($this->getRequest()->getParam('sitemap_id')) {
                $model->load($this->getRequest()->getParam('sitemap_id'));
                $model->removeFiles();
            }

            $model->setData($data);

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xsitemap')->__('Sitemap was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('sitemap_id' => $model->getId()));
                    return;
                }
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('sitemap_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('sitemap_id' => $this->getRequest()->getParam('sitemap_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('sitemap_id')) {
            try {
                $model = Mage::getModel('xsitemap/sitemap');
                $model->setId($id);
                /* @var $sitemap MageWorx_XSitemap_Model_Sitemap */
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('xsitemap')->__('Sitemap was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('sitemap_id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('xsitemap')->__('Unable to find a sitemap to delete'));
        $this->_redirect('*/*/');
    }

    public function generateAction()
    {
        $this->loadLayout();

        $sitemapId = intval($this->getRequest()->getParam('sitemap_id'));
        if($sitemapId){
            $sitemap = Mage::getModel('xsitemap/sitemap')->load($sitemapId);
            if (is_object($sitemap) && $sitemap->getId()){
                $filePath = $sitemap->getData('sitemap_filename');
            }
        }

        if(!empty($filePath)){
            $title = $this->__('Extended Sitemap'). ': ' . $this->__('File') . ' "' . $filePath. '" ' . $this->__('Generation') . '...';
        }else{
            $title = $this->__('Extended Sitemap'). ': ' . $this->__('XML Sitemap') . ' ' . $this->__('Generation') . '...';
        }

        $this->getLayout()->getBlock('convert_root_head')
                ->setTitle($title);
        $this->renderLayout();
    }

    public function runGenerateAction()
    {
        $sitemapId = intval($this->getRequest()->getParam('sitemap_id'));
        if (!$sitemapId) return false;

        $action = $this->getRequest()->getParam('action', '');
        if (!$action) return false;

        $sitemap = Mage::getModel('xsitemap/sitemap')->load($sitemapId);
        if (!$sitemap->getId()) return false;

        if ($action == "product") {
            $currentInc = intval($this->getRequest()->getParam('current_inc', 0));
            $sitemap->setCounter($currentInc);
        }

        try {
            $sitemap->generateXml($action);
            $result = $this->_rendererNextStepData($action, $sitemap);
        }
        catch (Exception $e) {
            $result           = $this->_rendererNextStepData($action, $sitemap);
            $result['errors'] = $result['text'] . " " . $e->getMessage() . " " . $this->__('Sitemap wasn\'t saved.');
            $result['stop']   = 1;
        }

        if (empty($result['errors'])) {
            $result['text'] .= $this->__("&nbsp;Ok");
        }

        $result['url'] = $this->getUrl('*/*/runGenerate/',
            array('sitemap_id'  => $sitemapId, 'action'      => $result['action'], 'current_inc' => $sitemap->getCounter()));

        if(is_callable(array(Mage::helper('core'), 'jsonEncode'))){
        	$body = Mage::helper('core')->jsonEncode($result);
        }else{
        	$body = Mage::helper('xsitemap/json')->jsonEncode($result);
        }

        $this->getResponse()->setBody($body);
    }

    protected function _rendererNextStepData($action, $sitemap)
    {
        switch ($action) {
            case 'category':
                $result['text']   = $this->__('Generated categories...');
                $result['action'] = 'product';
                break;
            case 'product':
                if ($sitemap->getCounter() >= $sitemap->getTotalProduct()) {
                    $result['text']   = $this->__('Generated products...');
                    $result['action'] = 'tag';
                }
                else {
                    $result['text']   = $this->__('Generating products, processed %2$s of %1$s products (%3$s%%)...',
                        $sitemap->getTotalProduct(), $sitemap->getCounter(),
                        round($sitemap->getCounter() * 100 / $sitemap->getTotalProduct(), 2)
                    );
                    $result['action'] = 'product';
                }
                break;
            case 'tag':
                $result['text']   = $this->__('Generated tags...');
                $result['action'] = 'cms';
                break;
            case 'cms':
                $result['text']   = $this->__('Generated CMS pages...');
                $result['action'] = 'additional_links';
                break;
            case 'additional_links':
                $result['text']   = $this->__('Generated additional links...');
            case 'blog':
                if ($action == 'blog') {
                    $result['text'] = $this->_getBlogStatusMessage();
                }
                if ($action != 'blog' && Mage::helper('xsitemap')->isBlogGenerateEnabled()) {
                    $result['action'] = 'blog';
                    return $result;
                }
            case 'fishpig_attribute_splash_pages':
                if ($action == 'fishpig_attribute_splash_pages') {
                    $result['text'] = $this->_getFishpigAttributeSplashPagesStatusMessage();
                }
                if ($action != 'fishpig_attribute_splash_pages' && Mage::helper('xsitemap')->isFishpigAttributeSplashGenerateEnabled()) {

                    $result['action'] = 'fishpig_attribute_splash_pages';
                    return $result;
                }
            case 'fishpig_attribute_splash_pro_pages':
                if ($action == 'fishpig_attribute_splash_pro_pages') {
                    $result['text'] = $this->_getFishpigAttributeSplashProPagesStatusMessage();
                }
                if ($action != 'fishpig_attribute_splash_pro_pages' && Mage::helper('xsitemap')->isFishpigAttributeSplashProGenerateEnabled()) {

                    $result['action'] = 'fishpig_attribute_splash_pro_pages';
                    return $result;
                }
            case 'sitemap_finish':
                if ($action != 'sitemap_finish') {
                    $result['action'] = 'sitemap_finish';
                    return $result;
                }
                $result['text']   = $this->__('Generated sitemap index...');
                $result['stop']   = 1;
                $this->_getSession()->addSuccess(Mage::helper('xsitemap')->__('Sitemap "%s" has been successfully generated',
                        $sitemap->getSitemapFilename()));
                $result['action'] = '';
                break;
        }

        return $result;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/xsitemap');
    }

    protected function _getBlogStatusMessage()
    {
        if ((string) Mage::getConfig()->getModuleConfig('AW_Blog')->active == 'true' || (string) Mage::getConfig()->getModuleConfig('Fishpig_Wordpress')->active ==
            'true') {
            return $this->__('Generated blog links...');
        }
        else {
            return $this->__('Blog module isn\'t installed or disabled...');
        }
    }

    protected function _getFishpigAttributeSplashPagesStatusMessage()
    {
        if ((string) Mage::getConfig()->getModuleConfig('Fishpig_AttributeSplash')->active == 'true') {
            if (Mage::helper('xsitemap')->isFishpigAttributeSplashGroupPagesEnabled()) {
                return $this->__('Generated fishpig attribute splash page and group links...');
            }
            return $this->__('Generated fishpig attribute splash page links...');
        }
        return $this->__('Fishpig attribute splash page module isn\'t installed or disabled...');
    }

    protected function _getFishpigAttributeSplashProPagesStatusMessage()
    {
        if ((string) Mage::getConfig()->getModuleConfig('Fishpig_AttributeSplashPro')->active == 'true') {
            return $this->__('Generated fishpig attribute splash pro page links...');
        }
        return $this->__('Fishpig attribute splash pro page module isn\'t installed or disabled...');
    }

}