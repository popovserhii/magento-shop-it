<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Adminhtml_Mageworx_Seocrosslinks_CrosslinkController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_init();
        $this->renderLayout();
    }

    /**
     * New SEO Cross Linking action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id    = (int) $this->getRequest()->getParam('crosslink_id');
        $model = $this->_initCrosslinkInstance();

        if ($model->getId()) {
            $this->_title($model->getCrosslink());
        } else {
            $this->_title($this->__('New Cross Link'));
        }

        if ($model->getId() || $id == 0) {
            $this->_init();
            $this->_addContent($this->getLayout()->createBlock('mageworx_seocrosslinks/adminhtml_crosslink_edit'))
                ->_addLeft($this->getLayout()->createBlock('mageworx_seocrosslinks/adminhtml_crosslink_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Cross Link do not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save SEO Cross Linking action
     *
     */
    public function saveAction()
    {
        $helperFunction = Mage::helper('mageworx_seocrosslinks/function');
        $data           = $helperFunction->arrayMapRecursive('trim', $this->getRequest()->getPost());
        $helper         = Mage::helper('mageworx_seocrosslinks');
        $session        = Mage::getSingleton('adminhtml/session');

        if (empty($data)) {
            return $this->_redirect('*/*/index', array('_current' => true));
        }

        try {
            $model = $this->_initCrosslinkInstance();
            $this->_save($model, $data, (bool)$this->getRequest()->getParam('reduce_priority'));

            return $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
        }
        catch (Exception $e) {
            $session->addError($e->getMessage());
            return $this->_redirect('*/*/edit', array('crosslink_id' => $this->getRequest()->getParam('crosslink_id')));
        }
    }

    /**
     * Delete SEO Cross Linking action
     *
     */
    public function deleteAction()
    {
        $model   = $this->_initCrosslinkInstance();
        $helper  = Mage::helper('mageworx_seocrosslinks');
        $session = Mage::getSingleton('adminhtml/session');

        try {
            $model->delete();
            $session->addSuccess($helper->__('The Internal SEO Link has been deleted'));
        } catch (Exception $e) {
            $session->addError($e->getMessage());
        }

        $this->getResponse()->setRedirect($this->getUrl('*/*/index'));
    }

    protected function _init()
    {
        $this->_title(Mage::helper('core')->__('SEO Cross Links'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/seocrosslinks');
    }

    /**
     * Init crosslink model instance
     *
     * @return MageWorx_SeoCrossLinks_Model_Crosslink
     */
    protected function _initCrosslinkInstance()
    {
        $id    = (int)$this->getRequest()->getParam('crosslink_id');
        $model = Mage::getModel('mageworx_seocrosslinks/crosslink');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $message = Mage::helper('mageworx_seocrosslinks')->__('Unable to find SEO Cross Link by ID.');
                Mage::getSingleton('adminhtml/session')->addError($message);
                $this->_redirect('*/*/index');
            }
        }
        Mage::register('current_crosslink_instance', $model);
        return $model;
    }

    protected function _save($model, $data, $reducePriorityFlag)
    {
        $session  = Mage::getSingleton('adminhtml/session');
        $keywords = $this->_getKeywords($data);

        for($i = 0; $i < count($keywords); $i++) {
            if ($i == 0) {
                $model->addData($data);
                $model->setId($model->getOrigData('crosslink_id'));
            } else {
                if ($reducePriorityFlag && $data['priority'] > 0) {
                    $data['priority'] -= 1;
                }
                $model->setData($data);
            }
            $model->setKeyword($keywords[$i]);
            $model->save();
        }

        if (count($keywords) == 1) {
            $session->addSuccess(Mage::helper('mageworx_seocrosslinks')->__('Cross Link was successfully saved'));
        } else {
            $session->addSuccess(Mage::helper('mageworx_seocrosslinks')->__('Cross Links were successfully saved'));
        }
    }

    /**
     * Retrive list of keywords
     *
     * @param array $data
     * @return array
     */
    protected function _getKeywords($data)
    {
        $keywordsString = $data['keyword'];
        $keywordsArray = array_filter(preg_split('/\r?\n/', $keywordsString));
        $keywordsArray = array_map('trim', $keywordsArray);
        $keywordsArray = array_filter($keywordsArray);
        $keywordsArray = array_unique($keywordsArray);
        return (count($keywordsArray) > 1) ? array_values($keywordsArray) : array($data['keyword']);
    }

    public function massDeleteAction()
    {
        $this->_init();
        $ids = $this->getRequest()->getParam('crosslinks');

        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Cross Link(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seocrosslinks/crosslink')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were successfully deleted',
                        count($ids)));
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massChangeLinkTargetAction()
    {
        $this->_massInitAction('link_target');
        $this->_redirect('*/*/index');
    }

    public function massChangeUseInProductAction()
    {
        $this->_massInitAction('in_product');
        $this->_redirect('*/*/index');
    }

    public function massChangeUseInCategoryAction()
    {
        $this->_massInitAction('in_product');
        $this->_redirect('*/*/index');
    }

    public function massChangeUseInCmsPageAction()
    {
        $this->_massInitAction('in_cms_page');
        $this->_redirect('*/*/index');
    }

    public function massChangeUseInBlogAction()
    {
        $this->_massInitAction('in_blog');
        $this->_redirect('*/*/index');
    }

    public function massChangeReplacementCountAction()
    {
        $this->_massInitAction('replacement_count');
        $this->_redirect('*/*/index');
    }

    public function massChangePriorityAction()
    {
        $this->_massInitAction('priority');
        $this->_redirect('*/*/index');
    }

    public function massChangeStatusAction()
    {
        $this->_massInitAction('status');
        $this->_redirect('*/*/index');
    }

    protected function _massInitAction($param)
    {
        $ids = $this->getRequest()->getParam('crosslinks');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Cross Link(s)'));
        }
        else {
            try {
                //Models are created for use of events
                foreach ($ids as $id) {
                    $model = Mage::getModel('mageworx_seocrosslinks/crosslink')->load($id);
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
		return Mage::getSingleton('admin/session')->isAllowed('catalog/mageworx_seocrosslinks');
	}
}
