<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Adminhtml_Mageworx_Seoxtemplates_Template_BlogController extends MageWorx_SeoXTemplates_Controller_Adminhtml_Seoxtemplates_Template
{
    public function indexAction()
    {
         if(!Mage::helper('mageworx_seoxtemplates/template_blog')->isEnabled()){
            $link = '<a target="_blank" href="http://ecommerce.aheadworks.com/magento-extensions/blog.html">' . $this->__('link') . '</a>';
            $message = $this->__('aheadWorks Blog extension is disabled or hasn\'t been installed. '
                . 'Make sure you have enabled it or download and install the module using this %s', $link);
            Mage::getSingleton('adminhtml/session')->addWarning($message);
            $this->_redirect('adminhtml/dashboard/index');
            return;
        }

        return parent::indexAction();
    }

    /**
     * Save action
     *
     */
    public function saveAction()
    {
        $this->_init();
        $data = $this->getRequest()->getPost();
        $id   = (int) $this->getRequest()->getParam('template_id');

        if ($data) {
            if ($this->getRequest()->getParam('prepare')) {
                $params = array(
                    'type_id' => $data['general']['type_id'],
                );
                $this->_redirect('*/*/edit', $params);
                return;
            }

            $this->_model->setData($data['general']);

            if ($id) {
                $this->_model->setId($id);
            }

            if ($this->_getHelper()->isAssignForAllItems($this->_model->getAssignType()) && $this->_model->getAllTypeDuplicateTemplate()) {
                Mage::getSingleton('adminhtml/session')->addWarning($this->__('The template cannot be saved. There is another template assigned for all blog posts.'));
                $this->_redirect('*/*/');
            }
            else {
                $this->_model->setDateModified(Mage::getSingleton('core/date')->gmtDate());
                $this->_model->save();
            }

            ///save related

            if ($this->_model->getId() && $this->_getHelper()->isAssignForAllItems($this->_model->getAssignType())) {
                try {
                    $this->_clearAllRelations();
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Template '%s' was successfully saved",
                            $this->_model->getName()));
                }
                catch (Exception $e) {
                    if ($e->getMessage()) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            }
            elseif ($this->_model->getId() && $this->_getHelper()->isAssignForIndividualItems($this->_model->getAssignType())) {

                $rawItemIds = $this->_prepareIndividualItemIds($data);
                $itemIds    = array_filter(array_unique($rawItemIds));

                $analogItemIds = $this->_model->getAssignForAnalogTemplateBlogIds();

                if (array_intersect(array_map('intval', $itemIds), array_map('intval', $analogItemIds))) {
                    Mage::getSingleton('adminhtml/session')->addWarning($this->__('The template was saved without assigned blog posts. Please add blog posts manually.'));
                    $this->_redirect('*/*/');
                    return;
                }

                try {
                    $this->_setTemplateIndividualItemRelation($itemIds);
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Template '%s' was successfully saved",
                            $this->_model->getName()));
                }
                catch (Exception $e) {
                    if ($e->getMessage()) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Create the blog posts grid for edit template action
     */
    public function templateGridAction()
    {
        $this->_initModel();
        $key = 'internal_in_products';
        $this->getRequest()->setPost($key, $this->getRequest()->getParam($key));

        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mageworx_seoxtemplates/adminhtml_template_blog_edit_tab_conditions')->toHtml()
        );
    }

    public function applyAction()
    {
        $this->loadLayout();
        $this->_initModel();

        $nextIds = $this->getRequest()->getParam('next_ids');

        $this->getLayout()->getBlock('apply')->setParams(
            array(
                'template' => $this->_model,
                'next_ids' => $nextIds,
                'test'     => $this->getRequest()->getParam('test')
            )
        );

        $this->getLayout()->getBlock('convert_root_head')
            ->setTitle($this->__('SEO XTemplates') . ': ' . $this->__($this->_model->getName()) . ' ' . $this->__('Generating') . '...');

        $this->renderLayout();
    }

    public function runApplyAction()
    {
        $this->_initModel();
        $nextIds = $this->getRequest()->getParam('next_ids');

        $reindex = $this->getRequest()->getParam('reindex', '');
        $test    = $this->getRequest()->getParam('test', '');

        if ($reindex) {
            $result = $this->_reindex($reindex, $nextIds);
        }
        elseif ($test == 'start') {
            $name = Mage::helper('mageworx_seoxtemplates')->getCsvFileName($this->_model);
            die($this->getUrl('*/*/csv/', array('_secure' => true, 'file_name' => $name)));
        }
        else {
            $result         = $this->_apply(null, $nextIds);
            $result['text'] = $this->__('Generating the \'%s\' template.', $this->_model->getName()) . ' ' . $result['text'];
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _apply($nestedStoreId = null, $nextIds = null)
    {
        $result = array();

        $testMode = $this->getRequest()->getParam('test', false);
        $current  = intval($this->getRequest()->getParam('current', 0));

        if ($current === 0 && !$testMode) {
            $this->_model->setDateApplyStart(date("Y-m-d H:i:s"))->save();
        }
        $limit = Mage::helper('mageworx_seoxtemplates/config')->getTemplateLimitForCurrentStore();

        $total = $this->_getTotalCount($nestedStoreId);

        if ($current <= $total) {

            $itemCollection = $this->_model->getItemCollectionForApply($current, $limit, false, $nestedStoreId);
            $this->_getHelper()->getTemplateAdapterByModel($this->_model)->apply($itemCollection, $this->_model,
                $nestedStoreId, $testMode);
            $current += $limit;

            if ($current >= $total) {
                $current = $total;

                $result['text'] = $this->__('Generation for has completed... ');
                $nextId         = $this->_getNextTemplateId($this->_model->getId(), $nextIds);

                if ($nextId) {
                    $result['url'] = $this->getUrl('*/*/runApply/',
                        array(
                        'template_id' => $nextId,
                        'next_ids'    => $nextIds,
                        'test'        => $this->getRequest()->getParam('test')
                        )
                    );
                }
                else {
                    if ($testMode) {
                        $result['url'] = $this->getUrl('*/*/runApply/',
                            array(
                            'test'        => 'start',
                            'method'      => $testMode,
                            'template_id' => $this->_model->getId(),
                            'next_ids'    => $nextIds,
                            )
                        );
                        $result['text'] .= $this->__('Go to report process...');
                    }
                    else {
                        $result['url'] = $this->getUrl('*/*/runApply/',
                            array(
                            'reindex'     => 'start',
                            'template_id' => $this->_model->getId(),
                            'next_ids'    => $nextIds,
                            )
                        );
                        $result['text'] .= $this->__('Go to reindex process...');
                    }
                }
                if (!$testMode) {
                    $this->_model->setDateApplyFinish(date("Y-m-d H:i:s"))->save();
                }
            }
            else {

                $result['url'] = $this->getUrl('*/*/runApply/',
                    array('current' => $current, 'template_id' => $this->_model->getId(), 'next_ids' => $nextIds, 'test' => $this->getRequest()->getParam('test')));

                $result['text'] = '';
                if (intval($this->getRequest()->getParam('current', 0)) == 0) {
                    $result['text'] = $this->__('Starting generation.');
                }

                $result['text'] = $this->__('Total %1$s, processed %2$s records (%3$s%%)...', $total, $current,
                    round($current * 100 / $total, 2));
            }
        }
        return $result;
    }

    /**
     *
     * @param array $itemIds
     */
    protected function _setTemplateIndividualItemRelation($itemIds)
    {
        $this->_clearAllRelations();
        $relation = $this->_model->getIndividualRelatedModel();

        if (is_array($itemIds) && !empty($itemIds)) {
            foreach ($itemIds as $itemId) {
                $relation->setData(array(
                    'template_id' => $this->_model->getId(),
                    'blog_id'     => $itemId
                    )
                );
                $relation->save();
            }
        }
    }

    /**
     * Retrive template model
     * @return MageWorx_SeoXTemplates_Model_Template_Blog
     */
    protected function _createModel()
    {
        return Mage::getModel('mageworx_seoxtemplates/template_blog');
    }

    /**
     * Delete all relations for model
     *
     */
    protected function _clearAllRelations()
    {
        if ($this->_model->getId()) {
            $relationIndividual = $this->_model->getIndividualRelatedModel();
            $relationIndividual->getResource()->deleteTemplateItemRelation($this->_model->getId());
        }
    }

}
