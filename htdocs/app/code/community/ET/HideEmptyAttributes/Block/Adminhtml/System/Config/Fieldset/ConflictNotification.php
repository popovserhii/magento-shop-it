<?php

/**
 * NOTICE OF LICENSE
 *
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_HideEmptyAttributes
 * @copyright  Copyright (c) 2015 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-commercial-v1/   ETWS Commercial License (ECL1)
 */

class ET_HideEmptyAttributes_Block_Adminhtml_System_Config_Fieldset_ConflictNotification
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'et_hideemptyattributes/conflict_notification.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

    /**
     * @return array|bool
     */
    public function getConflicts() {

        /** @var $helper ET_HideEmptyAttributes_Helper_Data*/
        $helper = Mage::helper('ethideemptyattributes');

        $isRewriteEnabled = $helper->isExtensionEnabled();
        if ($isRewriteEnabled) {
            $rewrites = array(
                array(
                    'magento_class' => 'Mage_Catalog_Block_Product_View_Attributes',
                    'module_class' => 'ET_HideEmptyAttributes_Block_Default',
                    'block_type' => 'catalog/product_view_attributes',
                    'node' => 'global/blocks/catalog/rewrite/product_view_attributes'
                ),
                array(
                    'magento_class' => 'Mage_Catalog_Block_Product_Compare_List',
                    'module_class' => 'ET_HideEmptyAttributes_Block_Compare',
                    'block_type' => 'catalog/product_compare_list',
                    'node' => 'global/blocks/catalog/rewrite/product_compare_list'
                ),
            );

            $conflicts = array();

            foreach ($rewrites as $rewrite) {
                $class = get_class($this->getLayout()->createBlock($rewrite['block_type']));
                //$class = (string)Mage::getConfig()->getNode($rewrite['node']);

                if ($class != $rewrite['module_class']) {
                    $conflicts[] = array('magento_class' => $rewrite['magento_class'], 'other_module_class' => $class);
                }
            }

            return $conflicts;
        }
        return false;
    }
}

