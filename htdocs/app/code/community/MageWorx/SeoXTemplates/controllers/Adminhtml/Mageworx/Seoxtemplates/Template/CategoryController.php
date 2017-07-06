<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Adminhtml_Mageworx_Seoxtemplates_Template_CategoryController extends MageWorx_SeoXTemplates_Controller_Adminhtml_Seoxtemplates_Template
{
    /**
     * Save action
     *
     */
    public function saveAction()
    {
        $this->_init();
        $data = $this->getRequest()->getPost();
        $id = (int) $this->getRequest()->getParam('template_id');

        if ($data) {
            if ($this->getRequest()->getParam('prepare')) {
                $params = array(
                    'type_id'  => $data['general']['type_id'],
                    'store' => $data['general']['store_id']
                );
                $this->_redirect('*/*/edit', $params);
                return;
            }

            $rawItemIds = $this->_prepareIndividualItemIds($data);
            $itemIds = array_filter(array_unique($rawItemIds));
            $this->_model->setData($data['general']);

            if ($id) {
                $this->_model->setId($id);
            }

            if($this->_getHelper()->isAssignForAllItems($this->_model->getAssignType()) && $this->_model->getAllTypeDuplicateTemplate()){
                Mage::getSingleton('adminhtml/session')->addWarning($this->__('The template cannot be saved. There is another template assigned for all products.'));
                $this->_redirect('*/*/');
            }else{
                $this->_model->setDateModified(Mage::getSingleton('core/date')->gmtDate());
                $this->_model->save();
            }

            ///save related
            if($this->_model->getId() && $this->_getHelper()->isAssignForAllItems($this->_model->getAssignType())){
                try {
                    $this->_clearAllRelations();
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Template '%s' was successfully saved", $this->_model->getName()));
                }
                catch (Exception $e) {
                    if ($e->getMessage()) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            }
            elseif($this->_model->getId() && $this->_getHelper()->isAssignForIndividualItems($this->_model->getAssignType())){

                $rawItemIds = $this->_prepareIndividualItemIds($data);
                $itemIds = array_filter(array_unique($rawItemIds));

                $analogItemIds = $this->_model->getAssignForAnalogTemplateCategoryIds();

                if(array_intersect(array_map('intval', $itemIds), array_map('intval', $analogItemIds))){
                    Mage::getSingleton('adminhtml/session')->addWarning($this->__('The template was saved without assigned categories. Please add categories manually.'));
                    $this->_redirect('*/*/');
                    return;
                }

                try {
                    //
                    $this->_setTemplateIndividualItemRelation($itemIds);
                    Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Template '%s' was successfully saved", $this->_model->getName()));
                }
                catch (Exception $e) {
                    if ($e->getMessage()) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            }

            $this->_redirect('*/*/');
            return;
        }
    }

    /**
     * AJAX category node loader
     */
    public function categoriesJsonAction()
    {
        $this->_initModel();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mageworx_seoxtemplates/adminhtml_template_category_edit_tab_conditions')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Set individual relation
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
                    'category_id' => $itemId
                    )
                );
                $relation->save();
            }
        }
    }

    /**
     * Retrive template model
     * @return MageWorx_SeoXTemplates_Model_Template_Category
     */
    protected function _createModel()
    {
        return Mage::getModel('mageworx_seoxtemplates/template_category');
    }

    /**
     * Delete all relations for model
     *
     */
    protected function _clearAllRelations()
    {
        if($this->_model->getId()){
            $relationIndividual = $this->_model->getIndividualRelatedModel();
            $relationIndividual->getResource()->deleteTemplateItemRelation($this->_model->getId());
        }
    }

}

