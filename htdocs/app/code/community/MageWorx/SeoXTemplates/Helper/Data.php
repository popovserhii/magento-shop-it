<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     *
     * @param string $path
     * @param string $name
     * @return string
     */
    public function getCsvFile($path, $name)
    {
        return $path . DS . $name . '.csv';
    }

    /**
     *
     * @return string
     */
    public function getCsvFilePath()
    {
        return Mage::getBaseDir('var');
    }

    /**
     * Retrive template hash
     * @param MageWorx_SeoXTemplates_Model_Template $template
     * @return string
     */
    public function getCsvFileName($template)
    {
        return md5($template->getName() . 'mageworx' . $template->getId());
    }

    /**
     * Retrive step of modification of the template by requested params
     * @return string
     */
    public function getStep()
    {
        if(!Mage::app()->getRequest()->getParam('template_id') && !Mage::app()->getRequest()->getParam('type_id')){
            return 'new_step_1';
        }
        elseif(!Mage::app()->getRequest()->getParam('template_id') && Mage::app()->getRequest()->getParam('type_id')){
            return 'new_step_2';
        }
        elseif(Mage::app()->getRequest()->getParam('template_id')){
            return 'edit';
        }
    }

    /**
     *
     * @param array $ids
     * @return string
     */
    public function getHideElementJsString($ids = array())
    {
        $string = '';
        foreach($ids as $id ){
            $string .= 'document.observe("dom:loaded", function(){$("' . $id . '").hide()});';
        }
        return $string;
    }
}
