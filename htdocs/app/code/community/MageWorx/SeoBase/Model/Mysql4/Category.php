<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Category extends Mage_Catalog_Model_Resource_Category
{
    /**
     * Retrieve attribute's raw value from DB for specified stores.
     *
     * @param int $entityId
     * @param array $attributes atrribute's ids or codes
     * @param array $storeIds
     * @return array|false
     */
    public function getAttributeRawValueByStores($entityId, $attributes, $storeIds)
    {
        if (!$entityId || empty($attributes)) {
            return false;
        }

        if (!in_array($this->getDefaultStoreId(), $storeIds)) {
            array_unshift($storeIds, $this->getDefaultStoreId());
        }

        $attributesData     = array();
        $staticAttributes   = array();
        $typedAttributes    = array();
        $staticTable        = null;
        $adapter            = $this->_getReadAdapter();

        foreach ($attributes as $_attribute) {
            /* @var $_attribute Mage_Catalog_Model_Entity_Attribute */
            $_attribute = $this->getAttribute($_attribute);
            if (!$_attribute) {
                continue;
            }
            $attributeCode = $_attribute->getAttributeCode();
            $attrTable     = $_attribute->getBackend()->getTable();
            $isStatic      = $_attribute->getBackend()->isStatic();

            if ($isStatic) {
                $staticAttributes[] = $attributeCode;
                $staticTable = $attrTable;
            } else {
                /**
                 * That structure needed to avoid farther sql joins for getting attribute's code by id
                 */
                $typedAttributes[$attrTable][$_attribute->getId()] = $attributeCode;
            }
        }

        /**
         * Collecting static attributes
         */
        if ($staticAttributes) {
            $select = $adapter->select()->from($staticTable, $staticAttributes)
                ->where($this->getEntityIdField() . ' = :entity_id');
            $attributesData = $adapter->fetchRow($select, array('entity_id' => $entityId));
        }

        /**
         * Collecting typed attributes, performing separate SQL query for each attribute type table
         */
        if ($typedAttributes) {
            foreach ($typedAttributes as $table => $_attributes) {

                $select = $adapter->select()
                    ->from(array('default_value' => $table), array('attribute_id', 'default_value.store_id'))
                    ->where('default_value.attribute_id IN (?)', array_keys($_attributes))
                    ->where('default_value.entity_type_id = :entity_type_id')
                    ->where('default_value.entity_id = :entity_id')
                    ->where('default_value.store_id IN(?)', $storeIds)
                    ->group(array('attribute_id', 'default_value.store_id'));

                $bind = array(
                    'entity_type_id' => $this->getTypeId(),
                    'entity_id'      => $entityId,
                );

                $select->columns(array('attr_value' => 'value'), 'default_value');
                $result = $adapter->fetchAll($select, $bind);

                foreach ($result as $data) {
                    $attrCode = $typedAttributes[$table][$data['attribute_id']];
                    $attributesData[$attrCode][$data['store_id']] = $data['attr_value'];
                }
            }
        }

        return $attributesData ? $attributesData : false;
    }
}