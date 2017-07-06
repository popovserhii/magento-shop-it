<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Rewrite_Enterprise_CmsConfig extends MageWorx_SeoCrossLinks_Model_Rewrite_Enterprise_CmsConfigResolver
{
    /**
     * Retrieves attributes for passed cms
     * type excluded from revision control.
     *
     * @return array
     */
    protected function _getRevisionControledAttributes($type)
    {
        if ($this instanceof MageWorx_SeoBase_Model_Rewrite_Enterprise_CmsConfig) {
            return parent::_getRevisionControledAttributes($type);
        }

        Mage::dispatchEvent('load_revision_controlled_attributes', array('object' => $this));

        if (isset($this->_revisionControlledAttributes[$type])) {
            return $this->_revisionControlledAttributes[$type];
        }
        return array();
    }

    /**
     * Add custom attributes to revision control
     *
     * @param string $type
     * @param string $attributeCode
     */
    public function addRevisionControlledAttribute($type, $attributeCode)
    {
        if (!isset($this->_revisionControlledAttributes[$type])) {
            $this->_revisionControlledAttributes[$type] = array($attributeCode);
        } else {
            $this->_revisionControlledAttributes[$type][] = $attributeCode;
        }
    }
}
