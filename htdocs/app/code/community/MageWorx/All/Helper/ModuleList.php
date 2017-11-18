<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Helper_ModuleList
{
    public function getModuleList()
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		sort($modules);

        $mageworxModules = array();
        foreach ($modules as $moduleName) {
            $name = explode('_', $moduleName, 2);
        	if (!isset($name) || !in_array($name[0], $this->_getVendors())) {
        		continue;
        	}

            $moduleData = $this->_prepareData($moduleName, Mage::getConfig()->getNode('modules/' . $moduleName)->asArray());
            $mageworxModules[$moduleName] = $moduleData;
        }

        return $mageworxModules;
    }

    public function getStructuredModuleList()
    {
        return $this->_getStructuredModules($this->getModuleList());
    }

    /**
     * Retrieve structured array with extension dependencies
     *
     * @param array $mageworxModules
     * @return array
     */
    protected function _getStructuredModules($mageworxModules)
    {
        $result = array();
        $noPersonalUseExtensions = array();

        foreach ($mageworxModules as $moduleName => $moduleData) {

            if (!empty($moduleData['absorbers']) && is_array($moduleData['absorbers']) && array_intersect_key($moduleData['absorbers'], $mageworxModules)) {
                continue;
            }

            if (empty($moduleData['includes']) || !is_array($moduleData['includes'])) {
                $result[$moduleName] = $moduleData;
                continue;
            }

            if ($moduleData['active'] == 'false') {
                continue;
            }

            foreach ($moduleData['includes'] as $childModuleName => $moduleStatus) {
                if (!empty($mageworxModules[$childModuleName])) {
                    $moduleData['includes'][$childModuleName] = $mageworxModules[$childModuleName];

                    if (!in_array($childModuleName, $this->_getCommonExtensions())) {
                        $noPersonalUseExtensions[] = $childModuleName;
                    }
                } else {
                    unset($moduleData['includes'][$childModuleName]);
                }
            }
            $result = array_merge(array($moduleName => $moduleData), $result);
        }

        return array_diff_key($result, array_flip(array_unique($noPersonalUseExtensions)));
    }

    /**
     * @return array
     */
    protected function _getCommonExtensions()
    {
        return array('MageWorx_All');
    }

    /**
     *
     * @return array
     */
    protected function _getVendors()
    {
        return array('MageWorx');
    }

    /**
     *
     * @param string $moduleName
     * @param array $data
     * @return array
     */
    protected function _prepareData($moduleName, array $data)
    {
        if (empty($data['extension_name'])) {
            if (!empty($data['name'])) {
                $data['extension_name'] = $data['name'];
            } else {
                $data['extension_name'] = $moduleName;
            }
        }
        return $data;
    }

}