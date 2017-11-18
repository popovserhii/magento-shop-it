<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoRedirects_Adminhtml_Mageworx_Seoredirects_Redirect_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Grid SEO Redirect action
     *
     * @return void
     */
    public function indexAction()
    {
        if (!array_key_exists('store', $this->getRequest()->getParams())) {
            return $this->_redirect('*/*/*', array('store' => Mage::helper('mageworx_seoredirects')->getDefaultStoreId()));
        }

        $this->_init();
        $this->renderLayout();
    }

    /**
     * Edit SEO Redirect action
     *
     * @return void
     */
    public function editAction()
    {
        $id    = (int) $this->getRequest()->getParam('redirect_id');
        $model = $this->_initInstance();

        if ($model->getId()) {
            $this->_title($model->getProductName());
        }

        if ($model->getId() || $id == 0) {
            $this->_init();
            $this->_addContent($this->getLayout()->createBlock('mageworx_seoredirects/adminhtml_redirect_product_edit'))
                ->_addLeft($this->getLayout()->createBlock('mageworx_seoredirects/adminhtml_redirect_product_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Product Redirect do not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save SEO Redirect action
     *
     * @return this
     */
    public function saveAction()
    {
        $data    = $this->getRequest()->getPost();
        $session = Mage::getSingleton('adminhtml/session');

        if (empty($data)) {
            return $this->_redirect('*/*/index', array('_current' => true));
        }

        try {
            $model = $this->_initInstance();
            $model->addData($data);
            $model->save();

            return $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'), array('store' => $this->_getStoreId()));
        }
        catch (Exception $e) {
            $session->addError($e->getMessage());
            return $this->_redirect('*/*/edit', array(
                    'redirect_id' => $this->getRequest()->getParam('redirect_id'),
                    'store' => $this->_getStoreId()
                )
            );
        }
    }

    /**
     * Delete SEO Redirect action
     *
     * @return void
     */
    public function deleteAction()
    {
        $model   = $this->_initInstance();
        $helper  = Mage::helper('mageworx_seoredirects');
        $session = Mage::getSingleton('adminhtml/session');

        try {
            $model->delete();
            $session->addSuccess($helper->__('SEO Redirect has been deleted'));
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }


    protected function _init()
    {
        $this->_title(Mage::helper('core')->__('SEO Redirects For Deleted Products'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/seoredirects');
    }

    /**
     * Init redirect model instance
     *
     * @return MageWorx_SeoRedirects_Model_Redirect
     */
    protected function _initInstance()
    {
        $id    = (int)$this->getRequest()->getParam('redirect_id');

        if ($id) {
            $model = Mage::getModel('mageworx_seoredirects/redirect_product');
            $model->load($id);

            if (!$model->getId()) {
                $message = Mage::helper('mageworx_seoredirects')->__('Unable to find SEO Product Redirect by ID');
                Mage::getSingleton('adminhtml/session')->addError($message);
                $this->_redirect('*/*/index');
            }

            Mage::register('current_redirect_instance', $model);
            return $model;
        }

        $message = Mage::helper('mageworx_seoredirects')->__('Unable to find SEO Product Redirect by ID');
        Mage::getSingleton('adminhtml/session')->addError($message);
        $this->_redirect('*/*/index');
    }

    /**
     * SEO Redirects mass delete action
     *
     * @return this
     */
    public function massDeleteAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('redirects');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Redirect(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seoredirects/redirect_product')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were successfully deleted',
                        count($ids)));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangeHitsAction()
    {
        $this->_massInitAction('hits');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangeCategoryAction()
    {
        $this->_massInitAction('category_id');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangePriorityCategoryAction()
    {
        $this->_massInitAction('priority_category_id');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    public function massChangeStatusAction()
    {
        $this->_massInitAction('status');
        $this->_redirect('*/*/index', array('store' => $this->_getStoreId()));
    }

    protected function _massInitAction($param)
    {
        $ids = $this->getRequest()->getParam('redirects');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Redirect(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seoredirects/redirect_product')->load($id);
                    $model->setData($param, $this->getRequest()->getParam($param))->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated',
                        count($ids)));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
    }

    protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('catalog/mageworx_seoredirects');
	}

    /**
     *
     * @return int
     */
    protected function _getStoreId()
    {
        return (int)$this->getRequest()->getParam('store');
    }
}
