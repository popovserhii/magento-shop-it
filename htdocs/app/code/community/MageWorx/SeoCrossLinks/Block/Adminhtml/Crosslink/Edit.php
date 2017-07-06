<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Block_Adminhtml_Crosslink_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId   = 'crosslink_id';
        $this->_blockGroup = 'mageworx_seocrosslinks';
        $this->_controller = 'adminhtml_crosslink';

        parent::__construct();

        $this->_addButton('save_with_reduce', array(
            'label'     => Mage::helper('salesrule')->__('Save with Reduced Priority'),
            'onclick'   => "editForm.submit($('edit_form').action + 'reduce_priority/1/')"
        ), 10);

        if ($this->getRequest()->getParam('crosslink_id')) {
            $this->_updateButton('delete', '',
                array(
                'label'      => Mage::helper('catalog')->__('Delete'),
                'onclick'    => "deleteConfirm('{$this->__('Are you sure you want to do this?')}', '{$this->getUrl('*/*/delete',
                    array('crosslink_id' => (int) $this->getRequest()->getParam('crosslink_id')))}')",
                'class'      => 'delete',
                'sort_order' => 8
            ));
        }
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('SEO Cross Link Edit');
    }
}
