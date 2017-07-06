<?php
/**
 * MageWorx
 * MageWorx SeoReports Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoReports
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */




class MageWorx_SeoReports_Block_Adminhtml_Grid_Renderer_Error extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $index  = $this->getColumn()->getIndex();
        $values = Mage::registry('error_types');
        $errors = array();

        switch ($index) {
            case 'meta_title_error':
                if ($row->getData('prepared_meta_title') == '' && isset($values['missing'])){
                    $errors[] = $this->htmlEscape($values['missing']);
                }
                if ($row->getData('meta_title_len') > Mage::helper('seoreports/config')->getMaxLengthMetaTitle() && isset($values['long'])){
                    $errors[] = $this->htmlEscape($values['long'] . ' (' . $row->getData('meta_title_len') . ')');
                }
                if ($row->getData('meta_title_dupl') > 1 && isset($values['duplicate'])){
                    $errors[] = $this->htmlEscape($values['duplicate']) . ' (<a href="' . $this->getUrl('*/*/duplicateView/',
                                    array('prepared_meta_title' => $row->getData('prepared_meta_title'), 'store' => $row->getData('store_id'))) . '" title="' . $this->__('View Duplicates') . '">' . $row->getData('meta_title_dupl') . '</a>)';
                }
                break;
            case 'name_error':
                if ($row->getData('name_dupl') > 1 && isset($values['duplicate'])){
                    $errors[] = $this->htmlEscape($values['duplicate']) . ' (<a href="' . $this->getUrl('*/*/duplicateView/',
                                    array('prepared_name' => $row->getData('prepared_name'), 'store' => $row->getData('store_id'))) . '" title="' . $this->__('View Duplicates') . '">' . $row->getData('name_dupl') . '</a>)';
                }
                break;
            case 'meta_descr_error':
                if ($row->getData('meta_descr_len') == 0 && isset($values['missing'])){
                    $errors[] = $this->htmlEscape($values['missing']);
                }
                if ($row->getData('meta_descr_len') > Mage::helper('seoreports/config')->getMaxLengthMetaDescription() && isset($values['long'])){
                    $errors[] = $this->htmlEscape($values['long'] . ' (' . $row->getData('meta_descr_len') . ')');
                }
                break;
            case 'url_error':
                if ($row->getData('url_dupl') > 1 && isset($values['duplicate'])){
                    $errors[] = $this->htmlEscape($values['duplicate']) . ' (<a href="' . $this->getUrl('*/*/duplicateView/',
                                    array('url' => $row->getData('url'), 'store' => $row->getData('store_id'))) . '" title="' . $this->__('View Duplicates') . '">' . $row->getData('url_dupl') . '</a>)';
                }
                break;
            case 'heading_error':
                if ($row->getData('prepared_heading') == '' && isset($values['missing'])){
                    $errors[] = $this->htmlEscape($values['missing']);
                }
                if ($row->getData('heading_dupl') > 1 && isset($values['duplicate'])){
                    $errors[] = $this->htmlEscape($values['duplicate']) . ' (<a href="' . $this->getUrl('*/*/duplicateView/',
                                    array('prepared_heading' => $row->getData('prepared_heading'), 'store' => $row->getData('store_id'))) . '" title="' . $this->__('View Duplicates') . '">' . $row->getData('heading_dupl') . '</a>)';
                }
                break;
        }

        return implode('<br/>', $errors);
    }

}
