<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Factory extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @var MageWorx_SeoXTemplates_Model_Template
     */
    protected $_model = null;

    /**
     *
     * @param MageWorx_SeoXTemplates_Model_Template $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Model_Template
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function getItemType()
    {
        if($this->_model instanceof MageWorx_SeoXTemplates_Model_Template_Product){
            return 'product';
        }elseif($this->_model instanceof MageWorx_SeoXTemplates_Model_Template_Category){
            return 'category';
        }elseif($this->_model instanceof MageWorx_SeoXTemplates_Model_Template_Blog){
            return 'blog';
        }else{
            throw new Exception($this->__('Unknow template model type.'));
        }
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template
     */
    public function getHelper()
    {
        return Mage::helper("mageworx_seoxtemplates/template_{$this->getItemType()}");
    }

    /**
     *
     * @return MageWorx_SeoXTemplates_Helper_Template_Comment
     */
    public function getCommentHelper()
    {
        return Mage::helper("mageworx_seoxtemplates/template_comment_{$this->getItemType()}");
    }

}
