<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Resource_Catalog_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache = array();

    /**
     * Init resource model (catalog/category)
     */
    protected function _construct()
    {
        $this->_init('catalog/category', 'entity_id');
    }

    /**
     * Get category (id => URL) array
     *
     * @param array $categoryIds
     * @param Mage_Core_Model_Store|int $store
     *
     * @return array
     */
    public function getCollection(array $categoryIds, $store)
    {
        $categoryUrls = array();

        if (empty($categoryIds)) {
            return array();
        }

        $storeId = ($store instanceof Mage_Core_Model_Store) ? (int)$store->getId() : (int)$store;

        $read = $this->_getReadAdapter();

        $this->_select = $read->select()->from(array('e' => $this->getMainTable()), array($this->getIdFieldName()));

        if (Mage::helper('mageworx_seoall/version')->isEeRewriteActive()) {

            $urlSuffix = $this->_getUrlSuffixForEE($storeId);

            $this->_select
            ->joinLeft(
                array('ecp' => $this->getTable('enterprise_catalog/category')),
                'ecp.category_id = e.entity_id ' . 'AND ecp.store_id = ' . $storeId,
                array()
            )
            ->joinLeft(array('euur' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                'ecp.url_rewrite_id = euur.url_rewrite_id AND euur.is_system = 1',
                array()
            )
            ->joinLeft(array('ecp2' => $this->getTable('enterprise_catalog/category')),
                'ecp2.category_id = e.entity_id AND ecp2.store_id = 0',
                array()
            )
            ->joinLeft(array('euur2' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                'ecp2.url_rewrite_id = euur2.url_rewrite_id',
                array('url' => 'concat( ' . $this->_getWriteAdapter()->getIfNullSql('euur.request_path', 'euur2.request_path') . ',"' . $urlSuffix . '")')
            );

        } else {
            $urConditions = array(
                'e.entity_id=ur.category_id',
                $this->_getWriteAdapter()->quoteInto('ur.store_id=?', $storeId),
                'ur.product_id IS NULL',
                $this->_getWriteAdapter()->quoteInto('ur.is_system=?', 1),
            );

            $this->_select->joinLeft(
                array('ur' => $this->getTable('core/url_rewrite')), join(' AND ', $urConditions),
                array('url' => 'request_path')
            );
        }

        $this->_select->where('e.' . $this->getIdFieldName() . ' IN(?)', $categoryIds);

        $this->_joinAttribute($storeId, 'name');
        $this->_addFilter($storeId, 'is_active', 1);
        $this->_select->order('level ASC');

        $query = $read->query($this->_select);

        while ($row   = $query->fetch()) {
            $resArray = array();
            $resArray['url']  = $this->_prepareUrl($row, $storeId);
            $resArray['name'] = !empty($row['name']) ? $row['name'] : '';
            $categoryUrls[$row[$this->getIdFieldName()]] = $resArray;
        }

        return $categoryUrls;
    }

    /**
     * Prepare URL
     *
     * @param array $categoryRow
     * @param int $storeId
     * @return string
     */
    protected function _prepareUrl(array $categoryRow, $storeId)
    {
        if (!empty($categoryRow['url'])) {
            $partUrl = $categoryRow['url'];
        } else {
            $partUrl = 'catalog/category/view/id/' . $categoryRow[$this->getIdFieldName()];
        }

        $rawUrl = Mage::app()->getStore($storeId)->getUrl();
        $storeBaseUrl =  (strpos($rawUrl, "?")) ? substr($rawUrl, 0, strpos($rawUrl, "?")) : $rawUrl;

        return trim($storeBaseUrl, '/') . '/' . $partUrl;
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     *
     * @return Zend_Db_Select
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = Mage::getSingleton('catalog/category')->getResource()->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal(),
                'backend_type'   => $attribute->getBackendType()
            );
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
                break;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('e.' . $attributeCode . $conditionRule, $value);
        }
        else {
            $this->_select->join(
                    array('t1_' . $attributeCode => $attribute['table']),
                    'e.entity_id=t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id=0', array()
                )
                ->where('t1_' . $attributeCode . '.attribute_id=?', $attribute['attribute_id']);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            }
            else {
                $this->_select->joinLeft(
                        array('t2_' . $attributeCode => $attribute['table']),
                        $this->_getWriteAdapter()->quoteInto('t1_' . $attributeCode . '.entity_id = t2_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.attribute_id = t2_' . $attributeCode . '.attribute_id AND t2_' . $attributeCode . '.store_id=?',
                            $storeId), array()
                    )
                    ->where('IFNULL(t2_' . $attributeCode . '.value, t1_' . $attributeCode . '.value)' . $conditionRule,
                        $value);
            }
        }

        return $this->_select;
    }

    protected function _joinAttribute($storeId, $attributeCode)
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = Mage::getSingleton('catalog/category')->getResource()->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = array(
                'entity_type_id'    => $attribute->getEntityTypeId(),
                'attribute_id'      => $attribute->getId(),
                'table'             => $attribute->getBackend()->getTable(),
                'is_global'         => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type'      => $attribute->getBackendType()
            );
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if ($attribute['backend_type'] != 'static') {

            if ($attribute['is_global']) {
                $this->_select->join(
                array('t1_'.$attributeCode => $attribute['table']),
                'e.entity_id=t1_'.$attributeCode.'.entity_id AND t1_'.$attributeCode.'.store_id=0',
                array($attributeCode => 't1_' . $attributeCode . '.value')
                )
                ->where('t1_'.$attributeCode.'.attribute_id=?', $attribute['attribute_id']);

            } else {

                $this->_select->join(
                array('t1_'.$attributeCode => $attribute['table']),
                'e.entity_id=t1_'.$attributeCode.'.entity_id AND t1_'.$attributeCode.'.store_id=0',
                array()
                )
                ->where('t1_'.$attributeCode.'.attribute_id=?', $attribute['attribute_id']);

                $this->_select->joinLeft(
                    array('t2_'.$attributeCode => $attribute['table']),
                    $this->_getWriteAdapter()->quoteInto('t1_'.$attributeCode.'.entity_id = t2_'.$attributeCode.'.entity_id AND t1_'.$attributeCode.'.attribute_id = t2_'.$attributeCode.'.attribute_id AND t2_'.$attributeCode.'.store_id=?', $storeId),
                    array($attributeCode => new Zend_Db_Expr("IFNULL(t2_" . $attributeCode .".value, t1_".$attributeCode.".value)"))
                );
            }
        }

        return $this->_select;
    }

    protected function _getUrlSuffixForEE($storeId)
    {
        $urlSuffix = Mage::helper('catalog/product')->getProductUrlSuffix($storeId);
        return $urlSuffix ? '.' . $urlSuffix : '';
    }
}