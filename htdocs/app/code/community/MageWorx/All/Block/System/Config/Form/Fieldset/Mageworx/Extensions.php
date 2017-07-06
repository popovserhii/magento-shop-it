<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Block_System_Config_Form_Fieldset_Mageworx_Extensions extends MageWorx_All_Block_System_Config_Form_Fieldset_Mageworx_Abstract
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
        $structuredMageWorxModules = Mage::helper('mageworx_all/moduleList')->getStructuredModuleList();

        foreach ($structuredMageWorxModules as $moduleName => $moduleData) {
            $html.= $this->_getFieldHtml($element, $moduleName, $moduleData);
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }

    /**
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getFooterHtml($element)
    {
        $html = parent::_getFooterHtml($element);
        $html = '<h4>' . $this->__('Installed MageWorx Extensions') . '</h4>' . $html;

        return $html;
    }

    /**
     *
     * @param type $fieldset
     * @param string $moduleName
     * @param array $moduleData
     * @param boolean $isChild
     * @return string
     */
    protected function _getFieldHtml($fieldset, $moduleName, $moduleData, $isChild = false)
    {
        $field = $fieldset->addField($moduleName . '_'. substr(md5(rand()), 0, 5), 'label',
            array(
                'label'         => $this->_getModuleLabel($moduleData, $isChild),
                'value'         => $this->_getModuleVersion($moduleData),
            ))->setRenderer($this->_getFieldRenderer());

        $childHtml = '';

        if (!$isChild && !empty($moduleData['includes']) && is_array($moduleData['includes'])) {
            foreach ($moduleData['includes'] as $childModuleName => $childModuleData) {
                $childHtml .= $this->_getFieldHtml($fieldset, $childModuleName, $childModuleData, true);
            }
        }

        return $field->toHtml() . $childHtml;
    }

   /**
    *
    * @param array $moduleData
    * @return string
    */
    protected function _getModuleVersion($moduleData)
    {
        if ($moduleData['active'] == 'false') {
            $moduleVersion = Mage::helper('catalog')->__('Disabled');
        } elseif (empty($moduleData['version'])) {
            $moduleVersion = Mage::helper('mageworx_all')->__('Deleted');
        } else {
            $moduleVersion = 'v' . $moduleData['version'];
        }
        return $moduleVersion;
    }

    /**
     *
     * @param array $extensionData
     * @param boolean $isChild
     * @return string
     */
    protected function _getModuleLabel($extensionData, $isChild)
    {
        if (!empty($extensionData['url'])) {
            $extensionName = '<a href="' . htmlspecialchars($extensionData['url']) . '" target="_blank">'
                . htmlspecialchars($extensionData['extension_name']) .
            '</a>';
        } else {
            $extensionName = $extensionData['extension_name'];
        }

        if (!empty($extensionData['url_m2'])) {
            $extensionName .= ' / <a href="' . htmlspecialchars($extensionData['url_m2']) . '" target="_blank">'
                . 'M2' .
            '</a>';
        }

        if ($isChild) {
            $moduleLabel = str_repeat('&nbsp;', 6) . $extensionName;
        } else {
            $moduleLabel = $extensionName;
        }
        return $moduleLabel;
    }
}
